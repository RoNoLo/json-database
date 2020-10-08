<?php

namespace RoNoLo\JsonStorage;

class Database98WorkflowTruncateTest extends DatabaseWorkflowTestBase
{
    public function testTruncateDatabase()
    {
        $this->db->truncateEverything();

        $this->assertEquals(0, $this->storePersons->count());
    }
}
