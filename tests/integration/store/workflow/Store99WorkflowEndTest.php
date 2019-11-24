<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Store99WorkflowEndTest extends StoreWorkflowTestBase
{
    public function testRemoveDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->deleteDir($this->repoTestPath);

        $this->assertFalse($flysystem->has($this->repoTestPath));

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json')) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json');
        }
    }
}


