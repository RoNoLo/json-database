<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use RoNoLo\JsonStorage\Database\Query;

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
                'age' => $this->randomAge(10, 30),
                'rating' => $this->randomStars()
            ]);
        }

        $query = new Query($db);

        $result = $query
            ->from('person')
            ->find([
                "age" => 25
            ])
            ->execute();

        $this->assertEquals(16, $result->count());
    }
}



