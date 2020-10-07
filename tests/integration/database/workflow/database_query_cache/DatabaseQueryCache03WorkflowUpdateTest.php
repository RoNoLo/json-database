<?php

namespace RoNoLo\JsonStorage;

class DatabaseQueryCache03WorkflowUpdateTest extends DatabaseQueryCacheWorkflowTestBase
{
    public function testUpdateAge99Database()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json');
        $data = json_decode($json, true);

        $result = Database\Result::fromJson($this->db, 'something', $data);

        foreach ($result as $id => $data) {
            $data->age = 99;

            $this->db->put('something', $data);
        }

        $this->assertTrue(true);
    }
}



