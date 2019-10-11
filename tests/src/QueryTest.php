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
    public function testRequestingDocumentsSimple()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "age" => [
                    "gt" => 20,
                    "lt" => 40,
                ],
                "phone" => [
                    "ne" => true
                ],
            ])
            ->execute()
        ;

        // $this->assertEquals(2, count($ids));
    }

    public function testRequestingDocumentsWithFields()
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
