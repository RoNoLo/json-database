<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;

abstract class StoreWorkflowTestBase extends TestBase
{
    protected $documents_amount = 10000;

    protected $repoTestPath;

    /** @var Store */
    protected $store;

    protected function setUp(): void
    {
        $this->documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

        $this->repoTestPath = 'store_workflow';

        $datastoreAdapter = new Local($this->datastorePath . '/' . $this->repoTestPath);

        $storeConfig = new Store\Config();
        $storeConfig->setAdapter($datastoreAdapter);

        $this->store = Store::create($storeConfig);
    }
}