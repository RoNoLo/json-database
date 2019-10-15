<?php

namespace RoNoLo\Flydb;

use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class ZipRepositoryTest extends TestBase
{
    public function testAddingWritingFindingFromZipArchiveRepository()
    {
        $adapter = new ZipArchiveAdapter($this->datastorePath . DIRECTORY_SEPARATOR . 'repo.zip');
        $config = new Config(['query_class' => Query::class]);
        $repo = new Repository('bernd', $config, $adapter);

        $repo->storeManyDataFromJsonFile($this->fixturesPath . DIRECTORY_SEPARATOR . 'query_1000_docs.json');

        // Find stuff
        $query = $repo->query();

        $result = $query->find([
            "age" => 20
        ])->execute();

        $this->assertEquals(37, $result->count());

        // Change stuff
        foreach ($result as $document) {
            $document->set('age', 99);

            $repo->storeDocument($document);
        }

        // Find again, but 0 results
        $result = $query->find([
            "age" => 20
        ])->execute();

        $this->assertEquals(0, $result->count());

        // Find again
        $result = $query->find([
            "age" => 99
        ])->execute();

        $repo->remove($result);

        // Find again
        $result = $query->find([
            "age" => 99
        ])->execute();

        $this->assertEquals(0, $result->count());
    }
}



