<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QueryConditionalsWithAndOrTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age > 20 AND age < 50 AND ( eyeColor = 'blue' OR favoriteFruit = 'apple' );
     */
    public function testRequestingDocumentsOrAndAnd()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => [
                    '$gt' => 20,
                    '$lt' => 50,
                ],
                '$or' => [
                    [
                        "eyeColor" => [
                            '$eq' => "blue",
                        ]
                    ],
                    [
                        "favoriteFruit" => [
                            '$eq' => "apple"
                        ]
                    ]
                ]
            ])
            ->execute()
        ;

        $expected = 319;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age > 20 AND age < 50 AND ( eyeColor = 'blue' OR favoriteFruit = 'apple' );
     */
    public function testRequestingDocumentsOrAndAndOr()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                '$and' => [
                    "age" => [
                        '$gt' => 20,
                        '$lt' => 50,
                    ],
                    '$or' => [
                        [
                            "eyeColor" => [
                                '$eq' => "blue",
                            ]
                        ],
                        [
                            "favoriteFruit" => [
                                '$eq' => "apple"
                            ]
                        ]
                    ]
                ]
            ])
            ->execute()
        ;

        $expected = 319;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * SELECT *
     * FROM store
     * WHERE (
     *   age > 20 AND age < 50 AND eyeColor = 'blue' AND (
     *     balance > 1000.0 OR isActive = true
     *   ) OR favoriteFruit = 'apple'
     * );
     */
    public function testRequestingDocumentsOrAndAndOrDeep()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                '$or' => [
                    [
                        "eyeColor" => [
                            '$eq' => "blue",
                        ],
                        "age" => [
                            '$gt' => 20,
                            '$lt' => 50,
                        ],
                        '$or' => [
                            [
                                "balance" => [
                                    '$gt' => 1000.0
                                ]
                            ],
                            [
                                "isActive" => true
                            ]
                        ]
                    ],
                    [
                        "favoriteFruit" => [
                            '$eq' => "apple"
                        ]
                    ]
                ]
            ])
            ->execute()
        ;

        $expected = 470;

        $this->assertEquals($expected, $result->count());
    }
}
