<?php

namespace RoNoLo\JsonStorage;

class DatabaseQueryCache02WorkflowQuery1Test extends DatabaseQueryCacheWorkflowTestBase
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

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json', json_encode($result));

        $this->assertCount(1600, $result);
    }
}



