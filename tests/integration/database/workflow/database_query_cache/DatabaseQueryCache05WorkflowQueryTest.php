<?php

namespace RoNoLo\JsonStorage;

class DatabaseQueryCache05WorkflowQueryTest extends DatabaseQueryCacheWorkflowTestBase
{
    public function testQueryAge99Database()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('something')
            ->find([
                "age" => 99
            ])
            ->execute();

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json', json_encode($result));

        $this->assertCount(1600, $result);
    }
}



