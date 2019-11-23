<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Database\Query;

class Database07WorkflowQueryTest extends DatabaseWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Query($this->db);

        $result = $query
            ->from('something')
            ->find([
                "age" => 99
            ])
            ->execute();

        $this->assertCount(0, $result);
    }
}



