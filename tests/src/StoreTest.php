<?php

namespace RoNoLo\Flydb;

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

            $id = $repo->store($data);

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

        $id = $repo->store($data);

        $result = $repo->read($id, true);

        $this->assertEquals($data, $result);
    }

    public function testDeletingDocument()
    {
        $repo = new Store($this->datastoreAdapter);

        $data = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT'
        ];

        $id = $repo->store($data);

        $result = $repo->remove($id);

        $this->assertTrue($result);
    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }
}
