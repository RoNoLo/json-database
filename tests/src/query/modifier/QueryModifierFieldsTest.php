<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Store\Query;

class QueryModifierFieldsTest extends QueryTestBase
{
    /**
     * SELECT picture, age, eyeColor FROM store ORDER BY index ASC;
     */
    public function testRequestingDocumentsWithFieldsToInclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["picture", "age", "eyeColor"])
            ->sort("index", "asc")
            ->limit(1)
            ->execute()
        ;

        $actually = $result[0];

        $expected = (object) [
            "picture" => "http://placehold.it/32x32",
            "age" => 60,
            "eyeColor" => "brown",
        ];

        $this->assertEquals($expected, $actually);
    }

    /**
     * SELECT picture, age, name.first AS firstname, name.last AS lastname FROM store ORDER BY index ASC;
     */
    public function testRequestingDocumentsWithComplexFieldsToInclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["picture", "age", "firstname" => "name.first", "lastname" => "name.last"])
            ->sort("index", "asc")
            ->limit(1)
            ->execute()
        ;

        $actually = $result[0];

        $expected = (object) [
            "picture" => "http://placehold.it/32x32",
            "age" => 60,
            "firstname" => "Lisa",
            "lastname" => "Combs",
        ];

        $this->assertEquals($expected, $actually);
    }

    /**
     * SELECT picture AS image, age AS years, eyeColor AS color FROM store ORDER BY index ASC;
     */
    public function testRequestingDocumentsWithFieldsToExclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["image" => "picture", "years" => "age", "color" => "eyeColor"])
            ->sort("index", "asc")
            ->limit(1)
            ->execute()
        ;

        $actually = $result[0];

        $expected = (object) [
            "image" => "http://placehold.it/32x32",
            "years" => 60,
            "color" => "brown",
        ];

        $this->assertEquals($expected, $actually);
    }
}
