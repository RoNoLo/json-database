<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class DatabaseReadTest extends TestBase
{
    public function testAddingPersonsWithHobbyReferencesAndReadingTheFullPersonObject()
    {
        $config = new Database\Config();

        $config->addStore('person', Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/person'))));
        $config->addStore('hobby', Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/hobby'))));

        $db = Database::create($config);

        $hobby1 = $db->put('hobby', [
            'name' => 'Music',
            'stars' => 4,
        ]);
        $hobby2 = $db->put('hobby', [
            'name' => 'Boxen',
            'stars' => 2,
        ]);
        $hobby3 = $db->put('hobby', [
            'name' => 'Movies',
            'stars' => 5,
        ]);

        $person1 = $db->put('person', [
            'firstname' => 'Ronald',
            'lastname' => 'Locke',
            'age' => 42,
            'hobbies' => [
                '$hobby:' . $hobby1,
                '$hobby:' . $hobby2,
            ]
        ]);
        $person2 = $db->put('person', [
            'firstname' => 'Tom',
            'lastname' => 'Locke',
            'age' => 31,
            'hobbies' => [
                '$hobby:' . $hobby2,
            ]
        ]);
        $person3 = $db->put('person', [
            'firstname' => 'Brain',
            'lastname' => 'Locke',
            'age' => 22,
            'hobbies' => [
                '$hobby:' . $hobby2,
                '$hobby:' . $hobby3,
            ]
        ]);
        $person4 = $db->put('person', [
            'firstname' => 'Jean',
            'lastname' => 'Locke',
            'age' => 12,
            'hobbies' => []
        ]);

        // Now read back The persons
        $personA = $db->read('person', $person1);
        $this->assertCount(2, $personA->hobbies);
        $this->assertEquals(4, $personA->hobbies[0]->stars);
        $this->assertEquals(2, $personA->hobbies[1]->stars);

        $personB = $db->read('person', $person2);
        $this->assertCount(1, $personB->hobbies);
        $this->assertEquals(2, $personB->hobbies[0]->stars);

        $personC = $db->read('person', $person3);
        $this->assertCount(2, $personC->hobbies);
        $this->assertEquals(2, $personC->hobbies[0]->stars);
        $this->assertEquals(5, $personC->hobbies[1]->stars);

        $personD = $db->read('person', $person4);
        $this->assertCount(0, $personD->hobbies);

        $db->truncateEverything();

        $adapter = new Local($this->datastorePath);
        $flysystem = new Filesystem($adapter);
        $flysystem->deleteDir('person');
        $flysystem->deleteDir('hobby');
    }
}



