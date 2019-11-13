<?php

namespace RoNoLo\JsonDatabase;

use Exception;

class ResultTest extends TestBase
{
    public function testCanCreateResult()
    {
        $json = file_get_contents(__DIR__ . '/../fixtures/query_1000_docs.json');
        $data = json_decode($json, true);

        foreach ($data as &$item) {
            $item['registered'] = (new \DateTime("-" . rand(0, 10000) . " days"))->format(DATE_ISO8601);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/../fixtures/query_1000_docs.json', $json);
    }
}
