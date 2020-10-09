<?php

namespace RoNoLo\JsonStorage;

/**
 * Test if these documents are removed.
 */
class Database13WorkflowQueryInterestsTest extends DatabaseWorkflowTestBase
{
    public function testQueryPersonsWithInterestBoardGamesDatabaseHumans()
    {
        $query = new Database\Query($this->db);

        $humans = $query
            ->from('humans')
            ->find([
                "interests.name" => "Board Games"
            ])
            ->sort("index", "asc")
            ->execute();

        $this->assertEquals(1000, $humans->total());
    }

    public function testQueryPersonsWithInterestOnWednesdayDatabaseHumans()
    {
        $query = new Database\Query($this->db);

        $humans = $query
            ->from('humans')
            ->find([
                "interests.days" => [
                    '$contains' => "we"
                ]
            ])
            ->sort("index", "asc")
            ->execute();

        $this->assertEquals(6000, $humans->total());
    }

    public function testQueryPersonsWithInterestNotOnWednesdayDatabaseHumans()
    {
        $query = new Database\Query($this->db);

        $humans = $query
            ->from('humans')
            ->find([
                "interests.days" => [
                    '$nc' => "we"
                ]
            ])
            ->sort("index", "asc")
            ->execute();

        $this->assertEquals(4000, $humans->total());
    }
}



