<?php

namespace RoNoLo\Flydb;

class Database
{
    private $stores;

    private $index;

    private $schema;

    public function addStore($name, Store $store)
    {
        $this->stores[$name] = $store;
    }

    public function addSchema($name, $schema)
    {
        $this->schema[$name] = $schema;
    }

    public function addIndex($name, $index)
    {
        $this->index[$name] = $index;
    }

    public function store($name, $document)
    {

    }
}