<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Store00WorkflowSetupTest extends StoreWorkflowTestBase
{
    public function testCreateDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->createDir($this->repoTestPath);

        $this->assertTrue($flysystem->has($this->repoTestPath));
    }
}

