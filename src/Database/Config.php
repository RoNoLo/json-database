<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Store;

class Config
{
    /** @var Store[] */
    private $stores = [];

    private $options = [];

    public function addStore(string $name, Store $store)
    {
        $this->stores[$name] = $store;

        return $this;
    }

    public function setOption(string $name, $value)
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