<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryModifierFieldsTest extends QueryTestBase
{
    public function testRequestingDocumentsWithFieldsToInclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["picture" => 1, "age" => 1, "name" => 1])
            ->execute()
        ;

        $expected = 423;

        $this->assertEquals($expected, $result->count());
    }

    public function testRequestingDocumentsWithFieldsToExclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["picture" => 0, "age" => 0])
            ->execute()
        ;

        $expected = 423;

        $this->assertEquals($expected, $result->count());
    }
}
