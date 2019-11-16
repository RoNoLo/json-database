<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DatabaseRuntimeException;
use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\DocumentNotStoredException;
use RoNoLo\JsonQuery\JsonQuery;

class Database implements DatabaseInterface
{
    /** @var StoreInterface[] */
    private $stores;

    private $index;

    /** @var StoreInterface */
    private $indexStore;

    public function addStore($name, StoreInterface $store)
    {
        $this->stores[$name] = $store;
    }

    public function setIndexStore(StoreInterface $store)
    {
        $this->indexStore = $store;
    }

    public function addIndex($storeName, $name, $fields)
    {
        $this->index[$storeName][$name] = $fields;
    }

    /** @inheritDoc */
    public function putMany(string $storeName, array $documents): array
    {
        // This will force an array as root
        $documents = json_decode(json_encode($documents));

        if (!is_array($documents)) {
            throw new DocumentNotStoredException("Your data was not an array of objects. (To store objects use ->put() instead.)");
        }

        // Note: We could speed up the index writing here, by somewhat
        // open and write the index only once, but an exception would kill
        // the whole index. At the moment the slower approch is fine.

        $ids = [];
        foreach ($documents as $document) {
            $ids[] = $this->put($storeName, $document);
        }

        return $ids;
    }

    /** @inheritDoc */
    public function put(string $storeName, $document): string
    {
        $store = $this->getStore($storeName);

        $id = $store->put($document);

        if (!isset($this->index[$storeName])) {
            return $id;
        }

        // Okay we have an index. We have to extract the value
        $jsonQuery = JsonQuery::fromData($document);

        foreach ($this->index[$storeName] as $name => $fields) {
            $index = [];
            foreach ($fields as $field) {
                $index[$field] = $jsonQuery->get($field);
            }

            $indexName = $storeName . '_' . $name;

            try {
                $indexDocument = $this->indexStore->read($indexName, true);
            } catch (DocumentNotFoundException $e) {
                // When Index is first created.
                $indexDocument = [
                    '__id' => $indexName
                ];
            }
            $indexDocument[$id] = $index;

            $this->indexStore->put($indexDocument);
        }

        return $id;
    }

    /** @inheritDoc */
    public function read(string $storeName, string $id, $assoc = false)
    {
        $store = $this->getStore($storeName);

        // Returned as pure JSON
        $document = $store->read($id, null);

        // Now we look for referenced documents
        $breaker = 10;
        while ($breaker--) {
            $nothing = true;
            foreach ($this->stores as $name => $refStore) {
                if (preg_match_all('/"\$' . $name . ':([0-9a-zA-Z]+)"/', $document, $matches)) {
                    foreach ($matches[0] as $match => $placeholder) {
                        try {
                            $refDocument = $refStore->read($matches[1][$match], null);
                        } catch (DocumentNotFoundException $e) {
                            // We will add an error document instead of an exception
                            $refDocument = json_encode([
                                '__id' => $matches[1][$match],
                                '__error' => $e->getMessage()
                            ]);
                        }

                        $document = str_replace($placeholder, $refDocument, $document);
                    }

                    $nothing = false;
                }
            }

            if ($nothing) {
                break;
            }
        }

        return json_decode($document, !!$assoc);
    }

    /** @inheritDoc */
    public function readMany(string $storeName, array $ids, $assoc = false, $check = true)
    {
        if (!$check) {
            return new DatabaseDocumentIterator($this, $storeName, $ids, [], $assoc);
        }

        $store = $this->getStore($storeName);

        $exists = [];
        foreach ($ids as $id) {
            if ($store->has($id)) {
                $exists[] = $id;
            }
        }

        return new DatabaseDocumentIterator($this, $storeName, $exists, [], $assoc);
    }

    /** @inheritDoc */
    public function remove(string $storeName, string $id)
    {
        $store = $this->getStore($storeName);

        $return = $store->remove($id);

        if ($return) {
            if (isset($this->index[$storeName])) {
                foreach ($this->index[$storeName] as $name => $fields) {
                    $indexName = $storeName . '_' . $name;

                    $indexDocument = $this->indexStore->read($indexName, true);

                    if (isset($indexDocument[$id])) {
                        unset($indexDocument[$id]);

                        $this->indexStore->put($indexDocument);
                    }
                }
            }
        }
    }

    /** @inheritDoc */
    public function removeMany(string $storeName, array $ids)
    {
        foreach ($ids as $id) {
            $this->remove($storeName, $id);
        }
    }

    /** @inheritDoc */
    public function truncate(string $storeName)
    {
        $store = $this->getStore($storeName);

        $store->truncate();

        if (isset($this->index[$storeName])) {
            foreach ($this->index[$storeName] as $name => $fields) {
                $indexName = $storeName . '_' . $name;

                $this->indexStore->remove($indexName);
            }
        }
    }

    private function getStore($name): StoreInterface
    {
        if (!isset($this->stores[$name])) {
            throw new DatabaseRuntimeException(sprintf("No store with name `%s` was previously added.", $name));
        }

        return $this->stores[$name];
    }
}