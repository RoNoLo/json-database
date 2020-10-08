<?php

namespace RoNoLo\JsonStorage;

/**
 * Removes documents from one store.
 */
class Database09WorkflowRemoveTest extends DatabaseWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_persons.json');
        $data = json_decode($json, true);

        $result = Database\Result::fromJson($this->db, 'persons', $data);

        $this->db->removeMany('persons', $result->getIds());

        $this->assertEquals(9840, $this->storePersons->count());
    }
}



