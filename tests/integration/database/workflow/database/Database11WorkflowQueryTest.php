<?php

namespace RoNoLo\JsonStorage;

/**
 * Test if these documents are removed.
 */
class Database10WorkflowQueryTest extends DatabaseWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $query = new Database\Query($this->db);

        $result = $query
            ->from('persons')
            ->find([
                "age" => 99
            ])
            ->execute();

        $this->assertCount(0, $result);
    }
}



