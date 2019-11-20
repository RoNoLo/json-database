<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use RoNoLo\JsonStorage\Database\Query;
use Symfony\Component\Filesystem\Filesystem;

define('STORE_JSON_OPTIONS', JSON_PRETTY_PRINT);

class DatabaseIndexTest extends DatabaseTestBase
{
    protected $db;

    protected function setUp(): void
    {
        $this->db = new Database();

        $this->db->addStore('person', new Store(new Local($this->datastorePath . '/person')));
        $this->db->setIndexStore(new Store(new Local($this->datastorePath . '/index')));
        $this->db->addIndex('person', 'age', [
            'age',
            'name',
            'balance'
        ]);

        $this->fillDatabase($this->db, 'person', $this->fixturesPath . DIRECTORY_SEPARATOR . 'store_1000_docs.json');
    }

    public function testCanQueryWithUseageOfIndex()
    {
        $query = new Query($this->db);

        $result = $query
            ->from('person')
            ->find([
                "age" => 20
            ])
            ->useIndex('age')
            ->execute();

        $this->assertEquals(16, $result->count());

        $query = new Query($this->db);

        $result = $query
            ->from('person')
            ->find([
                "age" => 20
            ])
            ->execute();

        $this->assertEquals(16, $result->count());
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



