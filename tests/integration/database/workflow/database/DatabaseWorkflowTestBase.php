<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;

abstract class DatabaseWorkflowTestBase extends TestBase
{
    protected $documents_amount = 100000;

    protected $repoTestPath;

    /** @var Store */
    protected $store;

    /** @var Database */
    protected $db;

    protected function setUp(): void
    {
        $this->documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

        $this->repoTestPath = 'database_workflow';

        $datastoreAdapter = new Local($this->datastorePath . '/' . $this->repoTestPath);

        $storeConfig = new Store\Config();
        $storeConfig->setAdapter($datastoreAdapter);

        $this->store = Store::create($storeConfig);

        $dbConfig = new Database\Config();
        $dbConfig->addStore('something', $this->store);

        $this->db = Database::create($dbConfig);
    }
}