<?php

namespace RoNoLo\JsonStorage;

class DatabaseQueryCache02WorkflowQuery2Test extends DatabaseQueryCacheWorkflowTestBase
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

        $this->assertCount(1600, $result);
    }
}



