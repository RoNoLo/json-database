<?php

namespace RoNoLo\Flydb;

use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use RoNoLo\Flydb\Format\JsonFormat;

class StoreImportJsonTest extends TestBase
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

    public function testImportJsonString()
    {
        $config = new Config();
        $repo = new Store('test', $config, $this->datastoreAdapter);

        $result = $repo
            ->getImportFactory()
            ->forFormat(JsonFormat::class)
            ->import('{"name": "Lisa"}')
        ;
    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }
}
