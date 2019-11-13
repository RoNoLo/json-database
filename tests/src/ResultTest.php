<?php

namespace RoNoLo\JsonDatabase;

class ResultTest extends TestBase
{
    public function testCanCreateResult()
    {
        return;

        $json = file_get_contents(__DIR__ . '/../fixtures/query_1000_docs.json');
        $data = json_decode($json, true);

        foreach ($data as &$item) {
            $item['balance'] = floatval($item['balance']);
            $item['latitude'] = floatval($item['latitude']);
            $item['longitude'] = floatval($item['longitude']);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/../fixtures/query_1000_docs.json', $json);
    }
}
