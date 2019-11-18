<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QueryConditionalsWithNotTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE IS NOT ( age = 20 AND phone = '12345' OR age = 40 );
     */
    public function testRequestingDocumentsSimpleDeepOr()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                '$not' => [
                    [
                        "age" => [
                            '$eq' => 20,
                        ],
                        "phone" => [
                            '$eq' => "12345",
                        ]
                    ],
                    [
                        "age" => [
                            '$eq' => 40
                        ]
                    ]
                ]
            ])
            ->execute()
        ;

        $expected = 984;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE IS NOT ( age = 20 AND eyeColor = 'brows' );
     */
    public function testRequestingDocumentsSimpleDeepOrKeyword()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                '$not' => [
                    "age" => [
                        '$eq' => 20,
                    ],
                    "eyeColor" => [
                        '$eq' => "brown",
                    ]
                ]
            ])
            ->execute()
        ;

        $expected = 992;

        $this->assertEquals($expected, $result->count());
    }
}
