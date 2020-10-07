<?php

namespace RoNoLo\JsonStorage;

class DatabaseQueryCache06WorkflowRemoveTest extends DatabaseQueryCacheWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json');
        $data = json_decode($json, true);

        $result = Database\Result::fromJson($this->db, 'something', $data);

        $this->db->removeMany('something', $result->getIds());

        $this->assertEquals(98400, $this->store->count());
    }
}



