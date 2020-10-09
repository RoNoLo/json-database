<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Exception\{DatabaseRuntimeException,
    DocumentNotFoundException,
    DocumentNotStoredException,
    QueryExecutionException};
use League\Flysystem\FileNotFoundException;
use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\Database\Config;
use RoNoLo\JsonStorage\Database\DatabaseConfig;
use RoNoLo\JsonStorage\Database\DocumentIterator;
use RoNoLo\JsonStorage\Database\QueryCache;

class Database
{
    const OPTION_REMOVE_REFERENCED_ID = 'remove_referenced_id';

    /** @var Store[] */
    protected $stores = [];

    /** @var DatabaseConfig */
    protected $config;

    /** @var QueryCache */
    protected $queryCache = null;

    public static function create(DatabaseConfig $config)
    {
        return new static($config);
    }

    protected function __construct(DatabaseConfig $config)
    {
        $this->config = $config;
        $this->stores = $config->getStores();

        if (isset($this->stores[QueryCache::STORE_NAME])) {
            $this->queryCache = new QueryCache($this);
        }

        try {
            $this->config->getOption(self::OPTION_REMOVE_REFERENCED_ID);
        } catch (DatabaseRuntimeException $e) {
            $this->config->setOption(Database::OPTION_REMOVE_REFERENCED_ID, true);
        }
    }

    /**
     * Stores many documents to the store.
     *
     * @param string $storeName
     * @param array $documents
     * @param bool $refCode
     *
     * @return array Of IDs
     * @throws DatabaseRuntimeException
     * @throws DocumentNotStoredException
     * @throws QueryExecutionException
     */
    public function putMany(string $storeName, array $documents, $refCode = false): array
    {
        // This will force an array as root
        $documents = json_decode(json_encode($documents));

        if (!is_array($documents)) {
            throw new DocumentNotStoredException("Your data was not an array of objects. (To store objects use ->put() instead.)");
        }

        $ids = [];
        foreach ($documents as $document) {
            $ids[] = $this->put($storeName, $document, $refCode);
        }

        return $ids;
    }

    /**
     * Stores a document or data structure to the store.
     *
     * It has to be a single document i.e. a \stdClass after converting it via json_encode().
     *
     * @param string $storeName
     * @param \stdClass|array $document
     * @param bool $refCode If true a reference string to use in another JSON is returned.
     *
     * @return string
     * @throws DatabaseRuntimeException
     * @throws DocumentNotStoredException
     * @throws QueryExecutionException
     */
    public function put(string $storeName, $document, $refCode = false): string
    {
        $store = $this->getStore($storeName);

        if ($this->hasQueryCache()) {
            $this->queryCache->truncate($storeName);
        }

        $id = $store->put($document);

        return $refCode ? '$' . $storeName . ':' . $id : $id;
    }

    /**
     * Reads a document from a store.
     *
     * @param string $storeName
     * @param string $id
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     *
     * @return \stdClass|array
     * @throws DatabaseRuntimeException
     * @throws DocumentNotFoundException
     */
    public function read(string $storeName, string $id, $assoc = false)
    {
        $store = $this->getStore($storeName);

        $documentJson = $store->read($id, null);
        $documentJson = $this->attachObjectReferences($documentJson);

        return json_decode($documentJson, !!$assoc);
    }

    /**
     * Reads documents from the store.
     *
     * @param string $storeName
     * @param array $ids
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     * @param bool $check If false, no documents exists check will be executed in advance, just the Iterator will be created.
     *
     * @return DocumentIterator
     * @throws DatabaseRuntimeException
     */
    public function readMany(string $storeName, array $ids, $assoc = false, $check = true)
    {
        if (!$check) {
            return new DocumentIterator($this, $storeName, $ids, [], $assoc);
        }

        $store = $this->getStore($storeName);

        $exists = [];
        foreach ($ids as $id) {
            if ($store->has($id)) {
                $exists[] = $id;
            }
        }

        return new DocumentIterator($this, $storeName, $exists, [], $assoc);
    }

    /**
     * Removes a document from the store.
     *
     * @param string $storeName
     * @param string $id
     *
     * @return void
     * @throws DatabaseRuntimeException
     * @throws QueryExecutionException
     */
    public function remove(string $storeName, string $id)
    {
        $store = $this->getStore($storeName);

        if ($this->hasQueryCache()) {
            $this->queryCache->truncate($storeName);
        }

        $store->remove($id);
    }

    /**
     * Removes many documents from the store.
     *
     * @param string $storeName
     * @param array|Database\Result $ids
     *
     * @return void
     * @throws DatabaseRuntimeException
     * @throws QueryExecutionException
     */
    public function removeMany(string $storeName, $ids)
    {
        if ($ids instanceof Database\Result) {
            $ids = $ids->getIds();
        }

        foreach ($ids as $id) {
            $this->remove($storeName, $id);
        }
    }

    /**
     * Removes all documents from the store.
     *
     * @param string $storeName
     *
     * @return mixed
     * @throws DatabaseRuntimeException
     */
    public function truncate(string $storeName)
    {
        $store = $this->getStore($storeName);

        $store->truncate();
    }

    /**
     * Truncates every store.
     *
     * @throws DatabaseRuntimeException
     */
    public function truncateEverything()
    {
        foreach ($this->stores as $storeName => $store) {
            $this->truncate($storeName);
        }
    }

    /**
     * Does this database uses a query cache.
     *
     * @return bool
     */
    public function hasQueryCache()
    {
        return !is_null($this->queryCache);
    }

    /**
     * Requests the QueryCache.
     *
     * @param string $storeName
     * @param array $query
     *
     * @return array|false|null
     */
    public function findQueryCache(string $storeName, array $query)
    {
        if ($storeName == QueryCache::STORE_NAME) {
            return false;
        }

        return $this->queryCache->find($storeName, $query);
    }

    /**
     * Writes to the QueryCache.
     *
     * @param $storeName
     * @param $query
     * @param $ids
     */
    public function putQueryCache(string $storeName, array $query, array $ids)
    {
        $this->queryCache->put($storeName, $query, $ids);
    }

    /**
     * Returns all documents for further processing.
     *
     * @param string|null $storeName
     * @param array $usedFields
     *
     * @return \Generator
     * @throws DatabaseRuntimeException
     * @throws FileNotFoundException
     */
    public function documentsGenerator(string $storeName = null, array $usedFields = []): \Generator
    {
        $store = $this->getStore($storeName);

        foreach ($store->documentsGenerator() as $documentJson) {
            yield $this->attachObjectReferences($documentJson);;
        }
    }

    public function idToReference(string $id, string $storeName)
    {
        return '$' . $storeName . ':' .$id;
    }

    public function referenceToId(string $reference)
    {
        if (preg_match('/"\$([a-z_]+):([0-9a-zA-Z]+)"/', $reference, $matches)) {
            return $matches[2][0];
        }

        throw new \InvalidArgumentException("The given reference was not a valid 'reference string'.");
    }

    /**
     * Returns the store by name.
     *
     * @param string $name
     *
     * @return Store
     * @throws DatabaseRuntimeException
     */
    protected function getStore(string $name): Store
    {
        if (!isset($this->stores[$name])) {
            throw new DatabaseRuntimeException(sprintf("No store with name `%s` was previously added.", $name));
        }

        return $this->stores[$name];
    }

    /**
     * Resolves all references to other store objects and adds them into the document.
     *
     * @param string $documentJson
     *
     * @return mixed|string
     */
    protected function attachObjectReferences(string $documentJson)
    {
        // Now we look for referenced documents
        $breaker = 10;
        while ($breaker--) {
            $nothing = true;
            foreach ($this->stores as $name => $refStore) {
                if (preg_match_all('/"\$' . $name . ':([0-9a-zA-Z]+)"/', $documentJson, $matches)) {
                    foreach ($matches[0] as $match => $placeholder) {
                        try {
                            $refDocument = $refStore->read($matches[1][$match], true);

                            // Removing the object __id from referenced documents.
                            if ($this->config->getOption(self::OPTION_REMOVE_REFERENCED_ID)) {
                                unset($refDocument['__id']);
                            }

                            $refDocument = json_encode($refDocument, defined('STORE_JSON_OPTIONS') ? intval(STORE_JSON_OPTIONS) : 0);
                        } catch (DocumentNotFoundException $e) {
                            // We will add an error document instead of an exception
                            $refDocument = json_encode([
                                '__id' => $matches[1][$match],
                                '__error' => $e->getMessage()
                            ], defined('STORE_JSON_OPTIONS') ? intval(STORE_JSON_OPTIONS) : 0);
                        }

                        $documentJson = str_replace($placeholder, $refDocument, $documentJson);
                    }

                    $nothing = false;
                }
            }

            if ($nothing) {
                break;
            }
        }

        return $documentJson;
    }
}