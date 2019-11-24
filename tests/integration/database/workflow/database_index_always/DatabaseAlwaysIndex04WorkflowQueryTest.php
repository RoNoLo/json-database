<?php

namespace RoNoLo\JsonStorage;

class DatabaseAlwaysIndex04WorkflowQueryTest extends DatabaseAlwaysIndexWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('something')
            ->find([
                "age" => 20
            ])
            ->execute();

        $this->assertEquals(0, count($result));
    }
}



