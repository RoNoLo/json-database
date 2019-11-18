<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use RoNoLo\JsonStorage\Store\Query;

class ZipStoreTest extends TestBase
{
    private $datastoreAdapter;

    private $repoTestFile = 'repo.zip';

    protected function setUp(): void
    {
        $this->datastoreAdapter = new ZipArchiveAdapter($this->datastorePath . DIRECTORY_SEPARATOR . $this->repoTestFile);
    }

    public function testAddingWritingFindingFromZipArchiveRepository()
    {
        $store = new Store($this->datastoreAdapter);

        $this->fillStore($store, $this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        // Find stuff
        $query = new Query($store);

        $result = $query->find([
            "age" => 20
        ])->execute();

        $this->assertEquals(16, $result->count());

        // Change stuff
        foreach ($result as $id => $data) {
            $data->age = 99;

            $store->put($data);
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
        $this->datastoreAdapter->getArchive()->close();

        if (file_exists($this->datastorePath . DIRECTORY_SEPARATOR . $this->repoTestFile)) {
            unlink($this->datastorePath . DIRECTORY_SEPARATOR . $this->repoTestFile);
        }
    }
}



