<?php

namespace RoNoLo\JsonStorage;

/**
 * Test if these documents are removed.
 */
class Database12WorkflowAddingInterestsTest extends DatabaseWorkflowTestBase
{
    public function testUpdatePersonsWithHobbiesDatabaseHumans()
    {
        $query = new Database\Query($this->db);

        $interests = $query
            ->from('interests')
            ->find([])
            ->sort("name", "asc")
            ->execute()
        ;

        $interests = $interests->getIds();

        $humans = $query
            ->from('humans')
            ->find([])
            ->sort("index", "asc")
            ->execute()
        ;

        $i = 0;
        foreach ($humans as $human) {
            $human->interests = $this->db->idToReference($interests[$i], 'interests');

            $this->db->put('humans', $human);

            $i++;
            if ($i >= count($interests)) {
                $i = 0;
            }
        }

        $this->assertTrue(true);
    }
}



