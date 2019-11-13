<?php

namespace RoNoLo\JsonDatabase;

use Exception;

class ResultTest extends TestBase
{
    public function testCanCreateResult()
    {
        $json = file_get_contents(__DIR__ . '/../fixtures/query_1000_docs.json');
        $data = json_decode($json, true);

        $i = 0;
        foreach ($data as &$item) {
            $item['index'] = $i;
            $i++;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(__DIR__ . '/../fixtures/query_1000_docs.json.gz', $json);
    }
}
