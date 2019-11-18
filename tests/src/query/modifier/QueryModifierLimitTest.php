<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QueryModifierLimitTest extends QueryTestBase
{
    /**
     * SELECT * FROM store LIMIT 55, PHP_INT_MAX;
     */
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

    /**
     * SELECT * FROM store LIMIT 55;
     */
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

    /**
     * SELECT * FROM store LIMIT 55, 45;
     */
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
