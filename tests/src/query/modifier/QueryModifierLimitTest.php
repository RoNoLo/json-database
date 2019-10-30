<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryModifierLimitTest extends QueryTestBase
{
    public function testRequestingDocumentsWithSkip()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->skip(55)
            ->execute()
        ;

        $expected = 945;

        $this->assertEquals($expected, $result->count());
    }

    public function testRequestingDocumentsWithLimit()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->limit(55)
            ->execute()
        ;

        $expected = 55;

        $this->assertEquals($expected, $result->count());
    }

    public function testRequestingDocumentsWithLimitAndSkip()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->limit(45)
            ->skip(55)
            ->execute()
        ;

        $expected = 45;

        $this->assertEquals($expected, $result->count());
    }
}
