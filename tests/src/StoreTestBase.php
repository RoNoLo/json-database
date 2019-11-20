<?php

namespace RoNoLo\JsonStorage;

class StoreTestBase extends TestBase
{
    protected function fillStore(Store $store, $filePath)
    {
        if (preg_match('/.+\.json.gz$/', $filePath)) {
            $data = json_decode(gzdecode(file_get_contents($filePath)));
        } else {
            $data = json_decode(file_get_contents($filePath));
        }

        $store->putMany($data);
    }
}