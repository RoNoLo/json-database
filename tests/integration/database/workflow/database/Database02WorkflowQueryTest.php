<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Database\Query;

class Database02WorkflowQueryTest extends DatabaseWorkflowTestBase
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

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json', json_encode($result));

        $this->assertCount(1600, $result);
    }
}



