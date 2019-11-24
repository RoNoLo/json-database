<?php

namespace RoNoLo\JsonStorage;

class Store08WorkflowTruncateTest extends StoreWorkflowTestBase
{
    public function testTruncateDatabase()
    {
        $this->store->truncate();

        $this->assertEquals(0, $this->store->count());
    }
}
