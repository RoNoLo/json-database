<?php

namespace RoNoLo\JsonStorage;

class DatabaseQueryCache08WorkflowTruncateTest extends DatabaseQueryCacheWorkflowTestBase
{
    public function testTruncateDatabase()
    {
        $this->db->truncateEverything();

        $this->assertEquals(0, $this->store->count());
    }
}
