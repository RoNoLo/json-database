<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

class QuerySimpleEqualManyTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsVerySimpleArray()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => 20,
                "gender" => "female"
            ])
            ->execute()
        ;

        $expected = 26;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsVerySimpleArrayEmptyResult()
    {
        $store = new Store(new MemoryAdapter());
        $this->fillStore($store, $this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = new Query($store);
        $result = $query
            ->find([
                "age" => 20,
                "phone" => "1234567",
                "name" => "Thomas"
            ])
            ->execute()
        ;

        $expected = 0;

        $this->assertEquals($expected, $result->count());
    }
}
