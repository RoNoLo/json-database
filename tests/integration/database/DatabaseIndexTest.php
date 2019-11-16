<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;

class DatabaseIndexTest extends TestBase
{
    public function testAddingPersonsAndFillingTheIndex()
    {
        define('STORE_JSON_OPTIONS', JSON_PRETTY_PRINT);

        $db = new Database();
        $db->addStore('person', new Store(new Local($this->datastorePath . '/person')));
        $db->setIndexStore(new Store(new Local($this->datastorePath . '/index')));
        $db->addIndex('person', 'age', [
            'age'
        ]);

        for ($i = 0; $i < 100; $i++) {
            $db->put('person', [
                'firstname' => $this->randomFirstname(),
                'lastname' => $this->randomLastname(),
                'age' => $this->randomAge(),
                'rating' => $this->randomStars()
            ]);
        }

        $foo = 1;
    }
}



