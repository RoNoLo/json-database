<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryConditionalsWithAndOrTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 AND ( phone = '12345' OR age = 40 );
     */
    public function testRequestingDocumentsOrAndAnd()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => [
                    '$eq' => 20,
                ],
                '$or' => [
                    [
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

        $expected = 45;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age = 20 AND ( phone = '12345' OR age = 40 );
     */
    public function testRequestingDocumentsOrAndAndOr()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => [
                    '$eq' => 20,
                ],
                '$or' => [
                    [
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

        $expected = 45;

        $this->assertEquals($expected, $result->count());
    }
}
