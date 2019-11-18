<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QueryConditionalsWithOrTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 AND phone = '12345' OR age = 40;
     */
    public function testRequestingDocumentsSimpleDeepOr()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
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
            ])
            ->execute()
        ;

        $expected = 16;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 AND phone = '12345' OR age = 40;
     */
    public function testRequestingDocumentsSimpleDeepOrKeyword()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                '$or' => [
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

        $expected = 16;

        $this->assertEquals($expected, $result->count());
    }
}
