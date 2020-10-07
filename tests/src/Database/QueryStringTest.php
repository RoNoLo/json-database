<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\TestBase;

class QueryStringTest extends TestBase
{
    /**
     * @param $expected
     * @param $conditions
     *
     * @dataProvider canExtractAllFieldsFromQueryDataProvider
     */
    public function testCanExtractAllFieldsFromQuery($expected, $conditions)
    {
        $actually = (new QueryFields())->parse($conditions);

        $this->assertEquals($expected, $actually);
    }

    public function canExtractAllFieldsFromQueryDataProvider()
    {
        return [
            [
                '',
                []
            ],
            [
                "age 20",
                [
                    "age" => 20,
                ]
            ],
            [
                ["age", "isActive"],
                [
                    "age" => 20,
                    "isActive" => false
                ]
            ],
            [
                ["age", "phone", "name.first"],
                [
                    "age" => 20,
                    "phone" => "1234567",
                    "name.first" => "Thomas"
                ]
            ],
            [
                ["age"],
                [
                    ["age" => 20],
                    ["age" => 30],
                    ["age" => 40]
                ]
            ],
            [
                ["age", "phone"],
                [
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
            ],
            [
                ["age", "phone"],
                [
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
                ]
            ],
            [
                ["age", "phone"],
                [
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
                ]
            ],
            [
                ["age", "eyeColor"],
                [
                    '$not' => [
                        "age" => [
                            '$eq' => 20,
                        ],
                        "eyeColor" => [
                            '$eq' => "brown",
                        ]
                    ]
                ]
            ],
            [
                ["registered"],
                [
                    "registered" => [
                        '$gt' => new \DateTime("2018-01-01T10:00:00"),
                        '$lt' => new \DateTime("2020-01-01T10:00:00"),
                    ],
                ]
            ],
            [
                ["age", "phone"],
                [
                    "age" => [
                        '$gt' => 20,
                        '$lt' => 30,
                    ],
                    "phone" => [
                        '$ne' => true
                    ],
                ]
            ],
            [
                ["age", "eyeColor", "favoriteFruit"],
                [
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
            ],
            [
                ["eyeColor", "age", "balance", "isActive", "favoriteFruit"],
                [
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
                ]
            ]
        ];
    }
}