<?php

namespace RoNoLo\JsonStorage;

/**
 * Updates values of database documents.
 */
class Database05WorkflowUpdateTest extends DatabaseWorkflowTestBase
{
    public function testUpdateAge99DatabasePersons()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_persons.json');
        $data = json_decode($json, true);

        $result = Database\Result::fromJson($this->db, 'persons', $data);

        foreach ($result as $id => $data) {
            $data->age = 99;

            $this->db->put('persons', $data);
        }

        $this->assertTrue(true);
    }
}



