<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

/**
 * Sets up the directories for that test series.
 */
class Database00WorkflowSetupTest extends DatabaseWorkflowTestBase
{
    public function testCreateDirectory()
    {
        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->createDir($this->repoTestPath);

        $this->assertTrue($flysystem->has($this->repoTestPath));
    }
}

