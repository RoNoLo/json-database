<?php

namespace RoNoLo\JsonStorage;

/**
 * Queries the databases for documents #3.
 *
 * Note: should be equally slow as the #1 and #2 query.
 */
class Database04WorkflowQuery3Test extends DatabaseWorkflowTestBase
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

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_persons.json', json_encode($result));

        $this->assertCount(160, $result);
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

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_humans.json', json_encode($result));

        $this->assertCount(160, $result);
    }
}



