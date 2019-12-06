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
    protected $indexes;

    public static function create(Config $config)
    {
        return new static($config->getStores(), $config->getIndexStore(), $config->getIndexes(), $config->getOptions());
    }

    protected function __construct($stores, $indexStore, $indexes, $options = [])
    {
        parent::__construct($stores, $options);

        $this->indexStore = $indexStore;
        $this->indexes = $indexes;
    }

    public function __destruct()
    {
        foreach ($this->indexes as $storeName => $indexMeta) {
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

        foreach ($this->indexes as $storeName => $indexMeta) {
            foreach ($this->getStore($storeName)->documentsGenerator() as $documentJson) {
                foreach ($indexMeta as $indexName => $fields) {
                    $indexKey = $storeName . '_' . $indexName;

                    $documentJson = $this->attachObjectReferences($documentJson);

                    $document = json_decode($documentJson);

                    // Okay we have an index. We have to extract the value
                    $jsonQuery = JsonQuery::fromData($document);

                    $index = [];
                    foreach ($fields as $field) {
                        $index[$field] = $jsonQuery->get($field);
                    }

                    $indexDocument[$document->__id] = $indexKey;
                }

                $this->index[$storeName][$indexName] = $indexDocument;
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
        if (!isset($this->indexes[$storeName])) {
            return;
        }

        // Okay we have an index. We have to extract the value
        $jsonQuery = JsonQuery::fromData($document);

        foreach ($this->indexes[$storeName] as $indexName => $fields) {
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
        if (!isset($this->indexes[$storeName])) {
            return;
        }

        if (!isset($this->index[$storeName])) {
            $this->loadIndex($storeName);
        }
    }

    protected function loadIndex(string $storeName)
    {
        foreach ($this->indexes[$storeName] as $indexName => $fields) {
            $indexKey = $storeName . '_' . $indexName;

            if (!$this->indexStore->has($indexKey)) {
                $this->rebuildIndex($storeName, $indexName, $fields);
            }

            $index = $this->indexStore->read($indexKey, true);
            $this->index[$storeName][$indexName] = $index;
        }
    }
}