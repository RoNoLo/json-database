<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QueryConditionalsWithMultipleFieldsTest extends QueryTestBase
{
    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE age > 20 AND age < 30 AND phone != '';
     */
    public function testRequestingDocumentsMultipleConditions()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "age" => [
                    '$gt' => 20,
                    '$lt' => 30,
                ],
                "phone" => [
                    '$ne' => true
                ],
            ])
            ->execute()
        ;

        $expected = 210;

        $this->assertEquals($expected, $result->count());
    }

    /**
     * This will test, if a simple returning of full documents works.
     * Notice, that the find() has no "selector" key. Just a _simple_ condition
     * query for all documents.
     *
     * SELECT * FROM store WHERE registered > '2018-01-01T10:00:00' AND registered < '2020-01-01T10:00:00';
     */
    public function testRequestingDocumentsDateCompare()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([
                "registered" => [
                    '$gt' => new \DateTime("2018-01-01T10:00:00"),
                    '$lt' => new \DateTime("2020-01-01T10:00:00"),
                ],
            ])
            ->execute()
        ;

        $expected = 65;

        $this->assertEquals($expected, $result->count());
    }
}
