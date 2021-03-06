<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class DatabaseQueryCache00WorkflowSetupTest extends DatabaseQueryCacheWorkflowTestBase
{
    public function testCreateDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->createDir($this->repoTestPath);

        $this->assertTrue($flysystem->has($this->repoTestPath));
    }
}

