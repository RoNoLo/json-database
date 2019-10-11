<?php

namespace RoNoLo\Flydb;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryTest extends TestBase
{
    /** @var Filesystem */
    private $flysystem;

    private $datastoreAdapter;

    private $repoTestPath = 'query';

    protected function setUp(): void
    {
        $adapter = new Local($this->datastorePath);

        $this->flysystem = new Filesystem($adapter);
        $this->flysystem->createDir($this->repoTestPath);

        $this->datastoreAdapter = new Local($this->datastorePath . '/' . $this->repoTestPath);
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\JsonCollectionImportException
     */
    public function testRequestingDocumentsVerySimple()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $conditions = $query
            ->find([
                "age" => 20,
            ])
            ->getConditions()
        ;

        $expected = [
            Query::LOGIC_AND => [
                ['$eq' => ['age' => 20]]
            ]
        ];

        $this->assertEquals($expected, $conditions);
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\JsonCollectionImportException
     */
    public function testRequestingDocumentsVerySimpleArray()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $conditions = $query
            ->find([
                "age" => 20,
                "phone" => "1234567",
                "name" => "Thomas"
            ])
            ->getConditions()
        ;

        $expected = [
            Query::LOGIC_AND => [
                ['$eq' => ['age' => 20]],
                ['$eq' => ['phone' => "1234567"]],
                ['$eq' => ['name' => "Thomas"]],
            ]
        ];

        $this->assertEquals($expected, $conditions);
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\JsonCollectionImportException
     */
    public function testRequestingDocumentsSimple()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $conditions = $query
            ->find([
                "age" => [
                    '$gt' => 20,
                    '$lt' => 40,
                ],
                "phone" => [
                    '$ne' => true
                ],
            ])
            ->getConditions()
        ;

        $expected = [
            Query::LOGIC_AND => [
                ['$gt' => ['age' => 20]],
                ['$lt' => ['age' => 40]],
                ['$ne' => ['phone' => true]]
            ]
        ];

        $this->assertEquals($expected, $conditions);
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\JsonCollectionImportException
     */
    public function testRequestingDocumentsSimpleDeepOr()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $conditions = $query
            ->find([
                "name" => [
                    '$eq' => "Thomas"
                ],
                '$or' => [
                    [
                        "age" => [
                            '$eq' => 20,
                        ]
                    ],
                    [
                        "age" => [
                            '$eq' => 40
                        ]
                    ]
                ]
            ])
            ->getConditions()
        ;

        $expected = [
            Query::LOGIC_AND => [
                [Query::LOGIC_AND => [['$eq' => ['name' => "Thomas"]]]],
                [Query::LOGIC_OR => [
                    [Query::LOGIC_AND => [['$eq' => ['age' => 20]]]],
                    [Query::LOGIC_AND => [['$eq' => ['age' => 40]]]],
                ]]
            ]
        ];

        $this->assertEquals($expected, $conditions);
    }


    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\JsonCollectionImportException
     */
    public function testRequestingDocumentsSimpleDeepAnd()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $conditions = $query
            ->find([
                '$and' => [
                    [
                        "age" => [
                            '$gt' => 20,
                        ]
                    ],
                    [
                        "age" => [
                            '$lt' => 40
                        ]
                    ]
                ],
            ])
            ->getConditions()
        ;

        $expected = [
            Query::LOGIC_AND => [
                [Query::LOGIC_AND => [['$gt' => ['age' => 20]]]],
                [Query::LOGIC_AND => [['$lt' => ['age' => 40]]]],
            ]
        ];

        $this->assertEquals($expected, $conditions);
    }

    public function testRequestingDocumentsWithFields()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "conditions" => [
                    [
                        "age" => [
                            "gt" => 10,
                            "lt" => 40,
                        ],
                        "phone" => [
                            "ne" => true
                        ],
                    ]
                ],
                "fields" => [
                    "picture", "age", "name"
                ],
                "sort" => [
                    "age" => "asc"
                ],
                "limit" => 5,
                "skip" => 5,
            ])
            ->execute()
        ;

        $this->assertEquals(2, count($ids));
    }

    public function testRequestingDocumentsWithSelectorSyntax()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "selector" => [
                    [
                        "age" => [
                            "gt" => 10,
                            "lt" => 40,
                        ],
                        "phone" => [
                            "ne" => true
                        ],
                    ]
                ],
            ])
            ->execute()
        ;

        $this->assertEquals(2, count($result));
    }

    public function testRequestingDocumentsWithOrQuery()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "selector" => [
                    [
                        "or" => [
                            [
                                "age" => [
                                    "gt" => 10,
                                    "lt" => 20,
                                ],
                                "phone" => [
                                    "ne" => true
                                ],
                            ],
                            [
                                "age" => [
                                    "gt" => 60
                                ]
                            ]
                        ],
                    ]
                ],
            ])
            ->execute()
        ;

        $this->assertEquals(2, count($result));
    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }
}
