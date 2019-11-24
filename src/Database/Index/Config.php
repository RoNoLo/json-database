<?php

namespace RoNoLo\JsonStorage\Database\Index;

use RoNoLo\JsonStorage\Store;

class Config
{
    /** @var Store[] */
    private $stores = [];

    /** @var Store */
    private $indexStore;

    private $indexes = [];

    private $options = [];

    /**
     * @param Store $indexStore
     *
     * @return Config
     */
    public function setIndexStore(Store $indexStore): self
    {
        $this->indexStore = $indexStore;

        return $this;
    }

    /**
     * @param string $storeName
     * @param string $indexName
     * @param array $fields
     *
     * @return Config
     */
    public function addIndex(string $storeName, string $indexName, array $fields): Config
    {
        $this->indexes[$storeName][$indexName] = $fields;

        return $this;
    }

    /**
     * @param string $name
     * @param Store $store
     *
     * @return Config
     */
    public function addStore(string $name, Store $store): Config
    {
        $this->stores[$name] = $store;

        return $this;
    }

    public function setOption(string $name, $value): Config
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function getStores(): array
    {
        return $this->stores;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}