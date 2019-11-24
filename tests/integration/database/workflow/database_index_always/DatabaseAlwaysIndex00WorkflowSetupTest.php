<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class DatabaseAlwaysIndex00WorkflowSetupTest extends DatabaseAlwaysIndexWorkflowTestBase
{
    public function testCreateDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->createDir($this->databaseTestPath);

        $this->assertTrue($flysystem->has($this->databaseTestPath));
    }
}

