<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QuerySimpleEqualManyTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 AND isActive = false;
     */
    public function testRequestingDocumentsVerySimpleArray()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => 20,
                "isActive" => false
            ])
            ->execute()
        ;

        $expected = 8;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 AND phone = '1234567' AND name.first = 'Thomas';
     */
    public function testRequestingDocumentsVerySimpleArrayEmptyResult()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => 20,
                "phone" => "1234567",
                "name.first" => "Thomas"
            ])
            ->execute()
        ;

        $expected = 0;

        $this->assertEquals($expected, $result->count());
    }
}
