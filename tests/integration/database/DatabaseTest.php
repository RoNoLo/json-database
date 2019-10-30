<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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
        $db->addSchema('person', [
            'hobby' => [
                'store' => 'hobby',
                'id' => 'name'
            ]
        ]);
        $db->addIndex('person', [
            'age'
        ]);

        $this->fillDb($db, $this->fixturesPath . DIRECTORY_SEPARATOR . 'database_20.json');

        // Find stuff
        $query = new Query($store);

        $result = $query->find([
            "age" => 20
        ])->execute();

        $this->assertEquals(51, $result->count());

        // Change stuff
        foreach ($result as $id => $data) {
            $data->age = 99;

            $store->store($data, $id);
        }

        // Find again, but 0 results
        $result = $query->find([
            "age" => 20
        ])->execute();

        $this->assertEquals(0, $result->count());

        // Find again
        $result = $query->find([
            "age" => 99
        ])->execute();

        $store->removeMany($result->getIds());

        // Find again
        $result = $query->find([
            "age" => 99
        ])->execute();

        $this->assertEquals(0, $result->count());
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



