<?php

namespace RoNoLo\JsonStorage;

class Database08WorkflowQueryTest extends DatabaseWorkflowTestBase
{
    public function testQueryAge99DatabasePersons()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('persons')
            ->find([
                "age" => 99
            ])
            ->execute();

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp_persons.json', json_encode($result));

        $this->assertCount(160, $result);
    }

    public function testQueryAge99DatabaseHumans()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('humans')
            ->find([
                "age" => 99
            ])
            ->execute();

        $this->assertCount(0, $result);
    }
}



