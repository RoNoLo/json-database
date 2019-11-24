<?php

namespace RoNoLo\JsonStorage;

class Store06WorkflowRemoveTest extends StoreWorkflowTestBase
{
    public function testQueryAge20Database()
    {
        $json = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'tmp.json');
        $data = json_decode($json, true);

        $result = Store\Result::fromJson($this->store, $data);

        $this->store->removeMany($result->getIds());

        $this->assertEquals(98400, $this->store->count());
    }
}



