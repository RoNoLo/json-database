<?php

namespace RoNoLo\JsonStorage;

class DatabaseAlwaysIndex98WorkflowRebuildIndexTest extends DatabaseAlwaysIndexWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $this->db->rebuildIndexes();

        // $this->assertCount(1600, $result);
    }
}



