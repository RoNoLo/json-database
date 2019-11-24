<?php

namespace RoNoLo\JsonStorage;

class DatabaseAlwaysIndex08WorkflowTruncateTest extends DatabaseAlwaysIndexWorkflowTestBase
{
    public function testTruncateDatabase()
    {
        $this->db->truncateEverything();

        $this->assertEquals(0, $this->store->count());
    }
}
