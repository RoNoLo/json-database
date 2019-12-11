<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Exception\{DatabaseRuntimeException,
    DocumentNotFoundException,
    DocumentNotStoredException,
    QueryExecutionException};
use League\Flysystem\FileNotFoundException;
use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\Database\Config;
use RoNoLo\JsonStorage\Database\DocumentIterator;

class Database
{
    /** @var Store[] */
    protected $stores = [];

    /** @var array */
    protected $options = [];

    public static function create(Config $config)
    {
        return new static($config->getStores(), $config->getOptions());
    }

    protected function __construct($stores, $options = [])
    {
        $this->stores = $stores;
        $this->options = [
            // Remove the document ID from referenced documents.
            'remove_referenced_id' => true,
        ] + $options;
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
     */
    public function put(string $storeName, $document, $refCode = false): string
    {
        $store = $this->getStore($storeName);

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
     * @throws DocumentNotFoundException
     * @throws DocumentNotStoredException
     */
    public function remove(string $storeName, string $id)
    {
        $store = $this->getStore($storeName);

        $store->remove($id);
    }

    /**
     * Removes many documents from the store.
     *
     * @param string $storeName
     * @param array $ids
     *
     * @return void
     * @throws DatabaseRuntimeException
     * @throws DocumentNotFoundException
     * @throws DocumentNotStoredException
     */
    public function removeMany(string $storeName, array $ids)
    {
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
     * Truncates every store and index.
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

    public function canUseIndex(string $storeName, array $usedFields) {
        return false;
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
                            if ($this->options['remove_referenced_id']) {
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