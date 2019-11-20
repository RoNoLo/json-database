<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Memory\MemoryAdapter;
use RoNoLo\JsonStorage\Store\Query;

class MemoryStoreTest extends StoreTestBase
{
    public function testAddingWritingFindingWithMemoryStore()
    {
        $store = new Store(new MemoryAdapter());

        $this->fillStore($store, $this->fixturesPath . DIRECTORY_SEPARATOR . 'store_1000_docs.json.gz');

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



