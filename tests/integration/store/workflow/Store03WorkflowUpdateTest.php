<?php

namespace RoNoLo\JsonStorage;

class Store03WorkflowUpdateTest extends StoreWorkflowTestBase
{
    public function testUpdateAge99Database()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json');
        $data = json_decode($json, true);

        $result = Store\Result::fromJson($this->store, $data);

        foreach ($result as $id => $data) {
            $data->age = 99;

            $this->db->put('something', $data);
        }

        $this->assertTrue(true);
    }
}



