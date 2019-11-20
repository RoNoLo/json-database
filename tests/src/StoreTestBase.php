<?php

namespace RoNoLo\JsonStorage;

class StoreTestBase extends TestBase
{
    protected function fillStore(Store $store, $filePath)
    {
        $data = json_decode(file_get_contents($filePath));

        $store->putMany($data);
    }
}