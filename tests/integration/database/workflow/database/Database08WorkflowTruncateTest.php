<?php

namespace RoNoLo\JsonStorage;

class Database08WorkflowTruncateTest extends DatabaseWorkflowTestBase
{
    public function testTruncateDatabase()
    {
        $this->db->truncateEverything();

        $this->assertEquals(0, $this->store->count());
    }
}
