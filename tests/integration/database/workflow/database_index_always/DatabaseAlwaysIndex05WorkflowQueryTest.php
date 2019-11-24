<?php

namespace RoNoLo\JsonStorage;

class DatabaseAlwaysIndex05WorkflowQueryTest extends DatabaseAlwaysIndexWorkflowTestBase
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



