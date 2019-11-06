<?php

namespace RoNoLo\JsonDatabase;

use Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\DocumentNotStoredException;

class StoreExceptionsTest extends TestBase
{
    public function testPutWillThrowExceptionWhenStoringArray()
    {
        $store = new Store(new MemoryAdapter());

        $this->expectException(DocumentNotStoredException::class);
        $this->expectExceptionMessage("Your data was not an single object. (Maybe an array, you may use ->putMany() instead.)");

        $data[] = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT',
            'random' => rand(100, 20000)
        ];

        $store->put($data);
    }

    public function testPutManyWillThrowExceptionWhenStoringObject()
    {
        $store = new Store(new MemoryAdapter());

        $this->expectException(DocumentNotStoredException::class);
        $this->expectExceptionMessage("Your data was not an array of objects. (To store objects use ->put() instead.)");

        $data = [
            'slug' => '123',
            'body' => 'THIS IS BODY TEXT',
            'random' => rand(100, 20000)
        ];

        $store->putMany($data);
    }

    public function testReadWillThrowExceptionWhenReadNonExistingDocument()
    {
        $store = new Store(new MemoryAdapter());

        $this->expectException(DocumentNotFoundException::class);
        $this->expectExceptionMessage("Document with id `123456` not found.");

        $store->read("123456");
    }
}
