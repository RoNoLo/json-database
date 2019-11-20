<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use RoNoLo\JsonStorage\Database\Query;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseIndexRebuildTest extends DatabaseTestBase
{
    protected $db;

    protected $indexStore;

    protected function setUp(): void
    {
        $this->indexStore = new Store(new Local($this->datastorePath . '/index'));

        $this->db = new Database();

        $this->db->addStore('person', new Store(new Local($this->datastorePath . '/person')));
        $this->db->setIndexStore($this->indexStore);
        $this->db->addIndex('person', 'age', [
            'age'
        ]);
        $this->db->addIndex('person', 'balance_name', [
            'balance',
            'name'
        ]);

        $this->fillDatabase($this->db, 'person', $this->fixturesPath . DIRECTORY_SEPARATOR . 'store_1000_docs.json.gz');
    }

    public function testRebuildIndex()
    {
        $db = $this->db;

        $person_age = $this->indexStore->read('person_age', true);
        $person_balance_name = $this->indexStore->read('person_balance_name', true);

        unset($person_age['__id']);
        unset($person_balance_name['__id']);

        $this->assertEquals(1000, count($person_age));
        $this->assertEquals(1000, count($person_balance_name));

        $this->indexStore->truncate();

        $this->assertEquals(0, $this->indexStore->count());
        $this->assertEquals(0, $this->indexStore->count());

        $db->rebuildIndexes();

        $person_age = $this->indexStore->read('person_age', true);
        $person_balance_name = $this->indexStore->read('person_balance_name', true);

        unset($person_age['__id']);
        unset($person_balance_name['__id']);

        $this->assertEquals(1000, count($person_age));
        $this->assertEquals(1000, count($person_balance_name));
    }

    protected function tearDown(): void
    {
        $this->db->truncateEverything();

        // Deleting left over root dirs
        $filesystem = new Filesystem();

        $dirs = [
            'index',
            'person',
            'hobby'
        ];

        foreach ($dirs as $dir) {
            if ($filesystem->exists($this->datastorePath . DIRECTORY_SEPARATOR . $dir)) {
                $filesystem->remove($this->datastorePath . DIRECTORY_SEPARATOR . $dir);
            }
        }
    }
}



