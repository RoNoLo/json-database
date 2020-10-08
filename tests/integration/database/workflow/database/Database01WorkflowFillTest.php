<?php

namespace RoNoLo\JsonStorage;

/**
 * This fills databases with data to work with.
 */
class Database01WorkflowFillTest extends DatabaseWorkflowTestBase
{
    public function testFillDatabases()
    {
        $persons = json_decode(gzdecode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'store_1000_docs.json.gz')));

        $j = 0;
        while (true) {
            foreach ($persons as $document) {
                $document->index = $j;
                $this->db->put('persons', $document);
                $this->db->put('humans', $document);

                $j++;

                if ($j >= $this->documents_amount) {
                    break 2;
                }
            }
        }

        $this->assertEquals($this->documents_amount, $this->storePersons->count());
        $this->assertEquals($this->documents_amount, $this->storeHumans->count());

        $interests = json_decode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'hobby_10.json'));

        foreach ($interests as $document) {
            $document->index = $j;
            $this->db->put('interests', $document);
        }

        $this->assertEquals(10, $this->storeInterests->count());
    }
}

