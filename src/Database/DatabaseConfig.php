<?php


namespace RoNoLo\JsonStorage\Database;


interface DatabaseConfig
{
    public function addStore(string $name, Store $store): Config;

    public function setOption(string $name, $value): Config;

    public function getOption(string $name);

    public function getOptions(): array;
}