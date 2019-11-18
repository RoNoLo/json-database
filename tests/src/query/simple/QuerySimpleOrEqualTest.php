<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QuerySimpleOrEqualTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 OR age = 30 OR age = 40;
     */
    public function testRequestingDocumentsVerySimpleOrCondition()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                ["age" => 20],
                ["age" => 30],
                ["age" => 40]
            ])
            ->execute()
        ;

        $expected = 53;

        $this->assertEquals($expected, $result->count());
    }
}
