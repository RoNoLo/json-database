<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;
use RoNoLo\JsonStorage\Exception\DatabaseRuntimeException;
use RoNoLo\JsonStorage\Store;

class Config implements DatabaseConfig
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

    public function getOption(string $name)
    {
        if (!isset($this->options[$name])) {
            throw new DatabaseRuntimeException(sprintf("The option %s was not defined.", $name));
        }

        return $this->options[$name];
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