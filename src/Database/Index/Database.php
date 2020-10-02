<?php

namespace RoNoLo\JsonStorage\Database\Index;

use League\Flysystem\FileNotFoundException;
use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\Database as BaseDatabase;
use RoNoLo\JsonStorage\Database\Config;
use RoNoLo\JsonStorage\Exception\DatabaseRuntimeException;
use RoNoLo\JsonStorage\Exception\DocumentNotFoundException;
use RoNoLo\JsonStorage\Exception\DocumentNotStoredException;
use RoNoLo\JsonStorage\Exception\QueryExecutionException;
use RoNoLo\JsonStorage\Store;

class Database extends BaseDatabase
{
    /** @var array */
    protected $index = [];

    /** @var Store */
    protected $indexStore;

    /** @var array */
    protected $indexSettings;

    public static function create(Config $config)
    {
        return new static($config->getStores(), $config->getIndexStore(), $config->getIndexes(), $config->getOptions());
    }

    protected function __construct($stores, $indexStore, $indexes, $options = [])
    {
        parent::__construct($stores, $options);

        $this->indexStore = $indexStore;
        $this->indexSettings = $indexes;
    }

    public function __destruct()
    {
        foreach ($this->indexSettings as $storeName => $indexMeta) {
            foreach ($indexMeta as $indexName => $fields) {
                $indexKey = $storeName . '_' . $indexName;

                $this->index[$storeName][$indexName]['__id'] = $indexKey;

                $this->indexStore->put($this->index[$storeName][$indexName]);
            }
        }
    }


    /**
     * Rebuilds all available indices for all stored data.
     *
     * @throws DatabaseRuntimeException
     * @throws DocumentNotStoredException
     * @throws FileNotFoundException
     */
    public function rebuildIndexes()
    {
        $this->indexStore->truncate();

        foreach ($this->indexSettings as $storeName => $indexMeta) {
            foreach ($this->getStore($storeName)->documentsGenerator() as $documentJson) {
                foreach ($indexMeta as $indexName => $fields) {
                    $documentJson = $this->attachObjectReferences($documentJson);

                    $document = json_decode($documentJson);

                    // Okay we have an index. We have to extract the value
                    $jsonQuery = JsonQuery::fromData($document);

                    foreach ($fields as $field) {
                        $this->index[$storeName][$indexName][$document->__id][$field] = $jsonQuery->get($field);
                    }
                }
            }
        }
    }

    public function getIndexMeta($storeName, $indexName)
    {
        $indexKey = $storeName . '_' . $indexName;

        if (!$this->indexStore->has($indexKey)) {
            throw new QueryExecutionException(sprintf("Index for store `%s` with name `%s` not found.", $storeName, $indexName));
        }

        return $this->indexStore->read($storeName . '_' . $indexName, true);
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
        $this->checkIndex($storeName);

        $id = parent::put($storeName, $document, false);

        $this->addToIndexes($storeName, $document, $id);

        return $refCode ? '$' . $storeName . ':' . $id : $id;
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
        parent::remove($storeName, $id);

        if (isset($this->indexMeta[$storeName])) {
            foreach ($this->indexMeta[$storeName] as $name => $fields) {
                $indexName = $storeName . '_' . $name;

                $indexDocument = $this->indexStore->read($indexName, true);

                if (isset($indexDocument[$id])) {
                    unset($indexDocument[$id]);

                    $this->indexStore->put($indexDocument);
                }
            }
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
        // We check if we have a index, which can be used.
        if (count($usedFields)) {
            $indexName = $this->canUseIndex($storeName, $usedFields);
            if ($indexName) {
                $this->loadIndex($storeName, $indexName);

                foreach ($this->index[$storeName][$indexName] as $id => $indexDocument) {
                    if ($id == '__id') {
                        continue;
                    }

                    yield $indexDocument;
                }

                return;
            }
        } else {
            $store = $this->getStore($storeName);

            foreach ($store->documentsGenerator() as $documentJson) {
                yield $this->attachObjectReferences($documentJson);;
            }
        }
    }

    public function canUseIndex(string $storeName, array $usedFields)
    {
        if (!isset($this->indexSettings[$storeName])) {
            return false;
        }

        foreach ($this->indexSettings[$storeName] as $indexKey => $fields) {
            $result = array_diff($usedFields, $fields);

            if (!count($result)) {
                return $indexKey;
            }
        }

        return false;
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
        parent::truncate($storeName);

        if (isset($this->indexMeta[$storeName])) {
            foreach ($this->indexMeta[$storeName] as $name => $fields) {
                $indexName = $storeName . '_' . $name;

                $this->indexStore->remove($indexName);
            }
        }
    }

    protected function addToIndexes(string $storeName, $document, string $id)
    {
        // Do we have an index definition?
        if (!isset($this->indexSettings[$storeName])) {
            return;
        }

        // Okay we have an index. We have to extract the value
        $jsonQuery = JsonQuery::fromData($document);

        foreach ($this->indexSettings[$storeName] as $indexName => $fields) {
            foreach ($fields as $field) {
                $this->index[$storeName][$indexName][$id][$field] = $jsonQuery->get($field);
            }
        }
    }

    protected function rebuildIndex(string $storeName, string $indexName, $fields)
    {
        $this->index[$storeName][$indexName] = [];
        foreach ($this->documentsGenerator($storeName) as $document) {
            $this->addToIndexes($storeName, $document, $id);
        }

        $this->index[$storeName][$indexName]['__id'] = $storeName . '_' . $indexName;

        $this->indexStore->put($this->index[$storeName][$indexName]);
    }

    protected function checkIndex(string $storeName)
    {
        if (!isset($this->indexSettings[$storeName])) {
            return;
        }

        if (!isset($this->index[$storeName])) {
            $this->loadIndex($storeName);
        }
    }

    protected function loadIndex(string $storeName, $indexName = null)
    {
        if (is_null($indexName)) {
            foreach ($this->indexSettings[$storeName] as $name => $fields) {
                $indexKey = $storeName . '_' . $name;

                if (!$this->indexStore->has($indexKey)) {
                    $this->rebuildIndex($storeName, $name, $fields);
                }

                $this->index[$storeName][$indexName] = $this->indexStore->read($indexKey, true);
            }
        } else {
            $indexKey = $storeName . '_' . $indexName;

            if (!$this->indexStore->has($indexKey)) {
                $this->rebuildIndex($storeName, $indexName, $this->indexSettings[$storeName][$indexName]);
            }

            $this->index[$storeName][$indexName] = $this->indexStore->read($indexKey, true);
        }
    }
}