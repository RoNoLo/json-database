<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Memory\MemoryAdapter;
use RoNoLo\JsonStorage\Database\Query;
use Symfony\Component\Filesystem\Filesystem;

define('STORE_JSON_OPTIONS', JSON_PRETTY_PRINT);

class DatabaseQueryTest extends DatabaseTestBase
{
    protected $db;

    protected function setUp(): void
    {
        $config = new Database\Config();

        $config->addStore('person', Store::create((new Store\Config())->setAdapter(new Local($this->datastorePath . '/person'))));
        $config->addStore('hobby', Store::create(((new Store\Config())->setAdapter(new Local($this->datastorePath . '/hobby')))));
        $config->addStore('country', Store::create(((new Store\Config())->setAdapter(new Local($this->datastorePath . '/country')))));

        $this->db = Database::create($config);

        $this->fillDatabaseWithRelations();
    }

    protected function tearDown(): void
    {
        $this->db->truncateEverything();

        // Deleting left over root dirs
        $filesystem = new Filesystem();

        $dirs = [
            'index',
            'person',
            'hobby',
            'country'
        ];

        foreach ($dirs as $dir) {
            if ($filesystem->exists($this->datastorePath . DIRECTORY_SEPARATOR . $dir)) {
                $filesystem->remove($this->datastorePath . DIRECTORY_SEPARATOR . $dir);
            }
        }
    }

    public function testCanQueryWithUseOfIndex()
    {
        $query = new Query($this->db);

        $result = $query
            ->from('person')
            ->find([
                "age" => 20
            ])
            // ->useIndex('age')
            ->execute();

        $this->assertEquals(16, $result->count());
    }

    public function testCanQueryDataWithoutQuery()
    {
        $query = new Query($this->db);

        $result = $query
            ->from('hobby')
            ->find([
                "days" => ['$in' => "mo"]
            ])
            ->execute();

        $this->assertEquals(16, $result->count());
    }

    private function fillDatabaseWithRelations()
    {
        $hobbies = json_decode(file_get_contents($this->fixturesPath . '/hobby_10.json'));
        $country = json_decode(file_get_contents($this->fixturesPath . '/country_5.json'));
        $persons = json_decode(file_get_contents($this->fixturesPath . '/persons_10.json'));

        $hobbyRefCodes = $this->db->putMany('hobby', $hobbies, true);
        $countryRefCodes = $this->db->putMany('country', $country, true);

        // Filling 10 persons by hand

        $sampson = $persons[0];
        $sampson->hobby = [
            $hobbyRefCodes[0],
            $hobbyRefCodes[3],
        ];
        $sampson->country = $countryRefCodes[0];

        $sampsonId = $this->db->put('person', $sampson);

        $mcclain = $persons[0];
        $mcclain->hobby = [
            $hobbyRefCodes[1],
            $hobbyRefCodes[4],
            $hobbyRefCodes[8],
        ];
        $mcclain->country = $countryRefCodes[3];

        $mcclainId = $this->db->put('person', $mcclain);

        $figueroa = $persons[0];
        $figueroa->hobby = [
            $hobbyRefCodes[8],
        ];
        $figueroa->country = $countryRefCodes[2];

        $figueroaId = $this->db->put('person', $figueroa);

        $henson = $persons[0];
        $henson->hobby = [
            $hobbyRefCodes[1],
            $hobbyRefCodes[4],
            $hobbyRefCodes[9],
            $hobbyRefCodes[0],
            $hobbyRefCodes[8],
        ];
        $henson->country = $countryRefCodes[0];

        $hensonId = $this->db->put('person', $henson);

        $mayer = $persons[0];
        $mayer->hobby = [
            $hobbyRefCodes[6],
            $hobbyRefCodes[2],
            $hobbyRefCodes[7],
        ];
        $mayer->country = $countryRefCodes[4];

        $mayerId = $this->db->put('person', $mayer);

        $obrien = $persons[0];
        $obrien->hobby = [];
        $obrien->country = $countryRefCodes[2];

        $obrienId = $this->db->put('person', $obrien);

        $bennett = $persons[0];
        $bennett->hobby = [
            $hobbyRefCodes[4],
        ];
        $bennett->country = $countryRefCodes[1];

        $bennettId = $this->db->put('person', $bennett);

        $harper = $persons[0];
        $harper->hobby = [
            $hobbyRefCodes[2],
            $hobbyRefCodes[4],
            $hobbyRefCodes[6],
            $hobbyRefCodes[8],
        ];
        $harper->country = $countryRefCodes[0];

        $harperId = $this->db->put('person', $harper);

        $clayton = $persons[0];
        $clayton->hobby = [
            $hobbyRefCodes[1],
            $hobbyRefCodes[2],
            $hobbyRefCodes[3],
        ];
        $clayton->country = $countryRefCodes[4];

        $claytonId = $this->db->put('person', $clayton);

        $reyes = $persons[0];
        $reyes->hobby = [
            $hobbyRefCodes[5],
            $hobbyRefCodes[7],
            $hobbyRefCodes[9],
            $hobbyRefCodes[2],
        ];
        $reyes->country = $countryRefCodes[0];

        $reyesId = $this->db->put('person', $reyes);
    }
}



