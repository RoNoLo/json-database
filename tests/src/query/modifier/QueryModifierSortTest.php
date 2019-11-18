<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

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
            ->limit(60)
            ->execute()
        ;

        $actually = $result[0];

        $this->assertEquals(0, $actually->index);
        $this->assertEquals("b5a93d8b-1523-4ce0-a850-cf8523bc50ac", $actually->guid);

        $actually = $result[50];

        $this->assertEquals(50, $actually->index);
        $this->assertEquals("660987eb-0a5e-4b07-9b01-f0c1a8b9c9a0", $actually->guid);
    }
}
