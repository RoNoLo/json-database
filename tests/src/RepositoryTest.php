<?php

namespace RoNoLo\Flydb;

use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;

class RespositoryTest extends TestBase
{
    private $flysystem;

    private $datastoreAdapter;

    private $repoTestPath = 'bernd';

    protected function setUp(): void
    {
        $adapter = new Local($this->datastorePath);
        $this->flysystem = new Filesystem($adapter);

        $this->flysystem->createDir($this->repoTestPath);

        $this->datastoreAdapter = new Local($this->datastorePath . '/' . $this->repoTestPath);
    }

    /**
     * @dataProvider validNameProvider
     */
    public function testValidRepoName($name)
    {
        $config = new Config();
        $repo = new Repository($name, $config, new NullAdapter());
        $this->assertSame($name, $repo->getName());
    }

    /**
     * @dataProvider invalidNameProvider
     */
    public function testInvalidRepoName($name)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('`' . $name . '` is not a valid repository name');

        $config = new Config();
        new Repository($name, $config, new NullAdapter());
    }

//    public function testGettingQueryObject()
//    {
//        $config = new Config();
//        $repo = new Repository('flywheeltest', $config, new NullAdapter());
//
//        $this->assertInstanceOf(Query::class, $repo->query());
//    }

    public function testStoringDocuments()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);

        for ($i = 0; $i < 5; $i++) {
            $data = [
                'slug' => '123',
                'body' => 'THIS IS BODY TEXT'
            ];

            $document = new Document($data);

            $id = $repo->store($document);

            $this->assertTrue(is_string($id));
        }
    }

    public function testReadDocument()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);

        $data = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT'
        ];

        $document = new Document($data);
        $id = $repo->store($document);

        $document = $repo->read($id);

        $payload = $document->getPayload();

        $this->assertSame($id, $document->getId());
        $this->assertSame($data, $payload);
    }

    public function testDeletingDocument()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);

        $data = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT'
        ];

        $document = new Document($data);
        $id = $repo->store($document);

        $result = $repo->delete($id);

        $this->assertTrue($result);
    }

    public function testFindDocuments()
    {
        $config = new Config();
        $repo = new Repository('test', $config, $this->datastoreAdapter);

        $dataList = [
            [
                'slug' => 123,
                'body' => 'Ronald Katja'
            ],
            [
                'slug' => 435234,
                'body' => 'Peter Robert'
            ],
            [
                'slug' => 546456,
                'body' => 'Lydia Hanna'
            ],
            [
                'slug' => 6757,
                'body' => 'Berta Charlotte'
            ],
            [
                'slug' => 3454,
                'body' => 'Jim Tim Tom'
            ],
        ];

        $repo->storeManyData($dataList);
        $query = $repo->query();
        $ids = $query
            ->find([
                'slug' => ['gt' => 2000, 'lt' => 10000],
                'body' => ['sw' => 'Ber']
            ])
            ->execute()
        ;

        $this->assertEquals(2, count($ids));
    }

    public function validNameProvider()
    {
        return [
            ['users'],
            ['photos_and_memories'],
            ['a12'],
            ['a_12'],
            ['a_12_10_12'],
            ['aaa'],
        ];
    }

    public function invalidNameProvider()
    {
        return [
            [''],
            ['12'],
            ['!!'],
            ['  '],
            ['This_would_be_a_valid_repository_name_except_for_the_fact_it_is_really_really_long'],
        ];
    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }
}
