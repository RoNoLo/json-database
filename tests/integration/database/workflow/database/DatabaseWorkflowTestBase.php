<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;

abstract class DatabaseWorkflowTestBase extends TestBase
{
    protected $documents_amount = 100000;

    protected $repoTestPath;

    /** @var Store */
    protected $storePersons;

    /** @var Store */
    protected $storeHumans;

    /** @var Store */
    protected $storeInterests;

    /** @var Database */
    protected $db;

    protected function setUp(): void
    {
        $this->documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

        $this->repoTestPath = 'database_workflow';

        $dbConfig = new Database\Config();

        $this->storePersons = Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/' . $this->repoTestPath . '/persons')));
        $this->storeHumans = Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/' . $this->repoTestPath . '/humans')));
        $this->storeInterests = Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/' . $this->repoTestPath . '/interests')));

        $dbConfig->addStore('persons', $this->storePersons);
        $dbConfig->addStore('humans', $this->storeHumans);
        $dbConfig->addStore('interests', $this->storeInterests);

        $this->db = Database::create($dbConfig);
    }
}