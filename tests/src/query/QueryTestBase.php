<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit\Framework\TestCase;

abstract class QueryTestBase extends TestBase
{
    protected $store;

    public function setUp(): void
    {
        $this->store = new Store(new MemoryAdapter());
        $this->fillStore($this->store, $this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');
    }
}