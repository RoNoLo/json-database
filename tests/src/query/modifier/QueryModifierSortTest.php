<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryModifierSortTest extends QueryTestBase
{
    /**
     * SELECT index, guid FROM store ORDER BY index ASC;
     */
    public function testRequestingDocumentsWithSort()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["index", "guid"])
            ->sort("index", "asc")
            ->execute()
        ;

        $actually = $result[0];

        $this->assertEquals(0, $actually->index);
        $this->assertEquals("35be1a95-da82-4181-8ebb-b86969a46def", $actually->guid);

        $actually = $result[50];

        $this->assertEquals(50, $actually->index);
        $this->assertEquals("87bcc60b-7b0b-464e-8902-823ea064b880", $actually->guid);
    }
}
