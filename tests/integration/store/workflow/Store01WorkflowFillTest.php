<?php

namespace RoNoLo\JsonStorage;

class Store01WorkflowFillTest extends StoreWorkflowTestBase
{
    public function testFillDatabase()
    {
        $data = json_decode(gzdecode(file_get_contents($this->fixturesPath . DIRECTORY_SEPARATOR . 'store_1000_docs.json.gz')));

        $j = 0;
        while (true) {
            foreach ($data as $document) {
                $document->index = $j;
                $this->store->put($document);

                $j++;

                if ($j >= $this->documents_amount) {
                    break 2;
                }
            }
        }

        $this->assertEquals($this->documents_amount, $this->store->count());
    }
}

