<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;

abstract class DatabaseQueryCacheWorkflowTestBase extends TestBase
{
    protected $documents_amount = 100000;

    protected $repoTestPath;

    protected $queryCacheTestPath;

    /** @var Store */
    protected $store;

    /** @var Database */
    protected $db;

    protected function setUp(): void
    {
        $this->documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

        $this->repoTestPath = 'database_workflow';
        $this->queryCacheTestPath = 'database_workflow_query_cache';

        $config = new Database\Config();

        $this->store = Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/' . $this->repoTestPath)));

        $config->addStore('something', $this->store);
        $config->addQueryCache(Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/' . $this->queryCacheTestPath))));

        $this->db = Database::create($config);
    }
}