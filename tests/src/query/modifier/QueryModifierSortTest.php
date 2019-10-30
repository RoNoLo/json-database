<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryModifierSortTest extends QueryTestBase
{
    public function testRequestingDocumentsWithSort()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["index", "guid"])
            ->sort("index", "asc")
            ->execute()
        ;

        $this->assertEquals($expected, $result->count());
    }
}
