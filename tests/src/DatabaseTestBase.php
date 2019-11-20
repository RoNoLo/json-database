<?php

namespace RoNoLo\JsonStorage;

class DatabaseTestBase extends TestBase
{
    protected function fillDatabase(Database $db, string $storeName, $filePath)
    {
        $data = json_decode(file_get_contents($filePath));

        $db->putMany($storeName, $data);
    }
}