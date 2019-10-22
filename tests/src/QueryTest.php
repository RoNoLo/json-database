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
     * @throws Exception\DocumentNotStoredException
     */
    public function testRequestingDocumentsGetAll()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
        $result = $query
            ->find([])
            ->execute()
        ;

        $expected = 1000;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * @throws Exception\DocumentNotStoredException
     */
    public function testRequestingDocumentsVerySimple()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
        $result = $query
            ->find([
                "age" => 20,
            ])
            ->execute()
        ;

        $expected = 51;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsVerySimpleArray()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
        $result = $query
            ->find([
                "age" => 20,
                "gender" => "female"
            ])
            ->execute()
        ;

        $expected = 26;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsVerySimpleArrayEmptyResult()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
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
     */
    public function testRequestingDocumentsSimple()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
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

        $expected = 423;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsDateCompare()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
        $result = $query
            ->find([
                "registered" => [
                    '$gt' => new \DateTime("2018-01-01T10:00:00"),
                    '$lt' => new \DateTime("2020-01-01T10:00:00"),
                ],
            ])
            ->execute()
        ;

        $expected = 305;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     */
    public function testRequestingDocumentsSimpleDeepOr()
    {
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
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
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
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
        $collection = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json'));

        $store = new Store($this->datastoreAdapter);
        $store->putMany($collection);

        $query = new Query($store);
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
