<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;

abstract class DatabaseAlwaysIndexWorkflowTestBase extends TestBase
{
    protected $documents_amount = 100000;

    protected $databaseTestPath;

    protected $indexTestPath;

    /** @var Store */
    protected $store;

    /** @var Store */
    protected $indexStore;

    /** @var Database */
    protected $db;

    protected function setUp(): void
    {
        $this->documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

        $this->databaseTestPath = 'database_workflow_index_always';
        $this->indexTestPath = 'database_workflow_index_always_index';

        $dataStoreAdapter = new Local($this->datastorePath . '/' . $this->databaseTestPath);
        $indexStoreAdapter = new Local($this->datastorePath . '/' . $this->indexTestPath);

        $storeConfig = new Store\Config();
        $storeConfig->setAdapter($dataStoreAdapter);

        $indexConfig = new Store\Config();
        $indexConfig->setAdapter($indexStoreAdapter);

        $this->store = Store::create($storeConfig);
        $this->indexStore = Store::create($indexConfig);

        $dbConfig = new Database\Config();
        $dbConfig->addStore('something', $this->store);
        $dbConfig->setIndexStore($this->indexStore);
        $dbConfig->addIndex('something', 'age', [
            "age"
        ]);

        $this->db = Database\Index\Database::create($dbConfig);
    }
}