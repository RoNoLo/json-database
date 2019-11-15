<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Memory\MemoryAdapter;

class MemoryStoreTest extends TestBase
{
    public function testAddingWritingFindingWithMemoryStore()
    {
        $store = new Store(new MemoryAdapter());

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
}



