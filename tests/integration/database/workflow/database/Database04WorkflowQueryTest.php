<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Database\Query;

class Database04WorkflowQueryTest extends DatabaseWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Query($this->db);

        $result = $query
            ->from('something')
            ->find([
                "age" => 20
            ])
            ->execute();

        $this->assertEquals(0, count($result));
    }
}



