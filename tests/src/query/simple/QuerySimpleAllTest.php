<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QuerySimpleAllTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store;
     */
    public function testRequestingDocumentsGetAll()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->execute()
        ;

        $expected = 1000;

        $this->assertEquals($expected, $result->count());
    }
}
