<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Database99WorkflowEndTest extends DatabaseWorkflowTestBase
{
    public function testRemoveDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->deleteDir($this->repoTestPath);

        $this->assertFalse($flysystem->has($this->repoTestPath));

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_persons.json')) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_persons.json');
        }

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_humans.json')) {
            unlink(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_humans.json');
        }
    }
}


