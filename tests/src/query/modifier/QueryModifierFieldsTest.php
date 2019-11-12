<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class QueryModifierFieldsTest extends QueryTestBase
{
    /**
     * SELECT picture, age, name FROM store ORDER BY index ASC;
     */
    public function testRequestingDocumentsWithFieldsToInclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["picture", "age", "name"])
            ->sort("index", "asc")
            ->execute()
        ;

        $actually = $result[0];

        $expected = (object) [
            "picture" => "http://placehold.it/32x32",
            "age" => 29,
            "name" => "Bentley Bentley",
        ];

        $this->assertEquals($expected, $actually);
    }

    /**
     * SELECT picture AS image, age AS years, name AS fullname FROM store ORDER BY index ASC;
     */
    public function testRequestingDocumentsWithFieldsToExclude()
    {
        $query = new Query($this->store);
        $result = $query
            ->find([])
            ->fields(["image" => "picture", "years" => "age", "fullname" => "name"])
            ->sort("index", "asc")
            ->execute()
        ;

        $actually = $result[0];

        $expected = (object) [
            "image" => "http://placehold.it/32x32",
            "years" => 29,
            "fullname" => "Bentley Bentley",
        ];

        $this->assertEquals($expected, $actually);
    }
}
