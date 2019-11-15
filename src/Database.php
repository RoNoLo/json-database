<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DatabaseRuntimeException;

class Database implements DatabaseInterface
{
    /** @var StoreInterface[] */
    private $stores;

    private $index;

    private $schema;

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

    public function addIndex($name, $index)
    {
        $this->index[$name] = $index;
    }

    public function getStore($name): StoreInterface
    {
        if (!isset($this->stores[$name])) {
            throw new DatabaseRuntimeException(sprintf("No store with name `%s` was previously added.", $name));
        }

        return $this->stores[$name];
    }

    /** @inheritDoc */
    public function putMany(string $store, array $documents): array
    {
        // TODO: Implement putMany() method.
    }

    /** @inheritDoc */
    public function put(string $store, $document): string
    {
        // TODO: Implement put() method.
    }

    /** @inheritDoc */
    public function read(string $store, string $id, $assoc = false)
    {
        // TODO: Implement read() method.
    }

    /** @inheritDoc */
    public function readMany(string $store, array $ids, $assoc = false, $check = true)
    {
        // TODO: Implement readMany() method.
    }

    /** @inheritDoc */
    public function remove(string $store, string $id)
    {
        // TODO: Implement remove() method.
    }

    /** @inheritDoc */
    public function removeMany(string $store, array $ids)
    {
        // TODO: Implement removeMany() method.
    }
}