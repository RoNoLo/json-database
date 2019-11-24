<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class DatabaseAlwaysIndex99WorkflowEndTest extends DatabaseAlwaysIndexWorkflowTestBase
{
    public function testRemoveDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->deleteDir($this->databaseTestPath);

        $this->assertFalse($flysystem->has($this->databaseTestPath));

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json')) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json');
        }
    }
}


