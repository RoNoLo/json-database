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
        $repo = new Store('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "age" => 20,
            ])
            ->execute()
        ;

        $expected = 37;

        $this->assertEquals($expected, $result->count());
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
        $repo = new Store('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "age" => 20,
                "gender" => "female"
            ])
            ->execute()
        ;

        $expected = 14;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\JsonCollectionImportException
     */
    public function testRequestingDocumentsVerySimpleArrayEmptyResult()
    {
        $config = new Config();
        $repo = new Store('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "age" => 20,
                "phone" => "1234567",
                "name" => "Thomas"
            ])
            ->execute()
        ;

        $expected = 0;

        $this->assertEquals($expected, $result->count());
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
        $repo = new Store('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
        $result = $query
            ->find([
                "age" => [
                    '$gt' => 20,
                    '$lt' => 30,
                ],
                "phone" => [
                    '$ne' => true
                ],
            ])
            ->execute()
        ;

        $expected = 30;

        $this->assertEquals($expected, $result->count());
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
        $repo = new Store('test', $config, $this->datastoreAdapter);
        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        $query = $repo->query();
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

        $expected = 10;

        $this->assertEquals($expected, $result->count());
    }

    public function testRequestingDocumentsWithFields()
    {
        $config = new Config();
        $repo = new Store('test', $config, $this->datastoreAdapter);
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

        $expected = 10;

        $this->assertEquals($expected, $result->count());
    }

    public function testRequestingDocumentsWithSelectorSyntax()
    {
        $config = new Config();
        $repo = new Store('test', $config, $this->datastoreAdapter);
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

        $expected = 10;

        $this->assertEquals($expected, $result->count());
    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }
}
