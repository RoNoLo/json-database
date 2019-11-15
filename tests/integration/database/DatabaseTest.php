<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

class DatabaseTest extends TestBase
{
    /** @var Filesystem */
    private $flysystem;

    private $datastoreAdapter;

    private $repoTestPath = 'repo';

    protected function setUp(): void
    {
        $adapter = new Local($this->datastorePath);

        $this->flysystem = new Filesystem($adapter);
        $this->flysystem->createDir($this->repoTestPath);

        $this->datastoreAdapter = new Local($this->datastorePath . '/' . $this->repoTestPath);
    }

    public function testAddingWritingFindingFromZipArchiveRepository()
    {
        $db = new Database();
        $db->addStore('person', new Store(new Local($this->datastorePath . '/person')));
        $db->addStore('hobby', new Store(new Local($this->datastorePath . '/hobby')));
        $db->setIndexStore(new Store(new Local($this->datastorePath . '/_index')));
        $db->addIndex('person', [
            'age'
        ]);

        $hobby1 = $db->put('hobby', [
            'name' => 'Music',
            'stars' => 4,
        ]);
        $hobby2 = $db->put('hobby', [
            'name' => 'Boxen',
            'stars' => 2,
        ]);
        $hobby3 = $db->put('hobby', [
            'name' => 'Movies',
            'stars' => 5,
        ]);

        $person1 = $db->put('person', [
            'firstname' => 'Ronald',
            'lastname' => 'Locke',
            'age' => 42,
            'hobbies' => [
                '$hobby:' . $hobby1,
                '$hobby:' . $hobby2,
            ]
        ]);
        $person2 = $db->put('person', [
            'firstname' => 'Tom',
            'lastname' => 'Locke',
            'age' => 31,
            'hobbies' => [
                '$hobby:' . $hobby2,
            ]
        ]);
        $person3 = $db->put('person', [
            'firstname' => 'Brain',
            'lastname' => 'Locke',
            'age' => 22,
            'hobbies' => [
                '$hobby:' . $hobby2,
                '$hobby:' . $hobby3,
            ]
        ]);
        $person4 = $db->put('person', [
            'firstname' => 'Jean',
            'lastname' => 'Locke',
            'age' => 12,
            'hobbies' => []
        ]);

    }

    protected function tearDown(): void
    {
        $this->flysystem->deleteDir($this->repoTestPath);
    }

    private function fillDb(Database $db, string $filePath)
    {
        $data = json_decode(file_get_contents($filePath));

        foreach ($data as $item) {
            $db->store('person', $item);
        }
    }
}



