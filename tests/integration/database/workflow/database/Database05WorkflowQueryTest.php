<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use RoNoLo\JsonStorage\Database\Query;

class Database05WorkflowQueryTest extends DatabaseWorkflowTestBase
{
    public function testQueryAge99Database()
    {
        $query = new Query($this->db);

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



