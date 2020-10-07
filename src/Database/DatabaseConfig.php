<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Store;

interface DatabaseConfig
{
    public function addStore(string $name, Store $store): Config;

    public function addQueryCache(Store $store): Config;

    public function setOption(string $name, $value): Config;

    public function getOption(string $name);

    public function getOptions(): array;

    public function getStores(): array;
}