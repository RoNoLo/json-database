<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Exception\DatabaseRuntimeException;
use RoNoLo\JsonStorage\Store;

class Config
{
    /** @var Store[] */
    private $stores = [];

    private $options = [];

    /**
     * @param string $name
     * @param Store $store
     *
     * @return Config
     * @throws DatabaseRuntimeException
     */
    public function addStore(string $name, Store $store): Config
    {
        $this->ensureValidName($name);

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

    private function ensureValidName(string $name)
    {
        if (!preg_match('/[a-z_]+/', $name)) {
            throw new DatabaseRuntimeException("The name has invalid characters. Only lowercase and underscore is allowed.");
        }
    }
}