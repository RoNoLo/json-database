<?php

namespace RoNoLo\JsonStorage;

class Store04WorkflowQueryTest extends StoreWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Store\Query($this->store);

        $result = $query
            ->find([
                "age" => 20
            ])
            ->execute();

        $this->assertEquals(0, count($result));
    }
}



