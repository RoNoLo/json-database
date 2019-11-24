<?php

namespace RoNoLo\JsonStorage;

class Store02WorkflowQueryTest extends StoreWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Store\Query($this->store);

        $result = $query
            ->find([
                "age" => 20
            ])
            ->execute();

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json', json_encode($result));

        $this->assertCount(1600, $result);
    }
}



