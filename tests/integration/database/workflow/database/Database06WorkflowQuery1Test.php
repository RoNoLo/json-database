<?php

namespace RoNoLo\JsonStorage;

/**
 * Queries the databases for documents #1 after they were updated in
 * one of the stores.
 */
class Database06WorkflowQuery1Test extends DatabaseWorkflowTestBase
{
    public function testQueryAge20DatabasePersons()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('persons')
            ->find([
                "age" => 20
            ])
            ->execute();

        $this->assertEquals(0, count($result));
    }

    public function testQueryAge20DatabaseHumans()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('humans')
            ->find([
                "age" => 20
            ])
            ->execute();

        $this->assertEquals(160, count($result));
    }
}



