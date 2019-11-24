<?php

namespace RoNoLo\JsonStorage;

class Store07WorkflowQueryTest extends StoreWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Store\Query($this->store);

        $result = $query
            ->find([
                "age" => 99
            ])
            ->execute();

        $this->assertCount(0, $result);
    }
}



