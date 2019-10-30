<?php

namespace RoNoLo\JsonDatabase;

use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;

class StoreTest extends TestBase
{
    /** @var Filesystem */
    private $flysystem;

    private $datastoreAdapter;

    private $repoTestPath = 'repo';

    protected function setUp(): void
    {
        $adapter = new Local($this->datastorePath);

        $this->flysystem = new Filesystem($adapter);
        $this->flysystem->createDir($this->repoTestPath);

        $this->datastoreAdapter = new Local($this->datastorePath . '/' . $this->repoTestPath);
    }

    public function testStoringDocuments()
    {
        $repo = new Store($this->datastoreAdapter);

        for ($i = 0; $i < 5; $i++) {
            $data = [
                'slug' => '123',
                'body' => 'THIS IS BODY TEXT'
            ];

            $id = $repo->put($data);

            $this->assertTrue(is_string($id));
        }
    }

    public function testReadDocument()
    {
        $repo = new Store($this->datastoreAdapter);

        $data = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT'
        ];

        $id = $repo->put($data);

        $result = $repo->read($id, true);

        $expected = $data;
        $expected['__id'] = $id;

        $this->assertEquals($expected, $result);
    }

    public function testDeletingDocument()
    {
        $repo = new Store($this->datastoreAdapter);

        $data = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT'
        ];

        $id = $repo->put($data);

        $result = $repo->remove($id);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }
}
