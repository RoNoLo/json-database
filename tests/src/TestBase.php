<?php

namespace RoNoLo\JsonDatabase;

use PHPUnit\Framework\TestCase;

abstract class TestBase extends TestCase
{
    protected $testsRoot;

    protected $fixturesPath;

    protected $datastorePath;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->testsRoot = realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests'
        );

        $this->fixturesPath = $this->testsRoot . DIRECTORY_SEPARATOR . 'fixtures';
        $this->datastorePath = $this->testsRoot . DIRECTORY_SEPARATOR . 'datastore';

        parent::__construct($name, $data, $dataName);
    }

    protected function fillStore(StoreInterface $store, $filePath)
    {
        $data = json_decode(file_get_contents($filePath));

        $store->putMany($data);
    }

    protected function randomFirstname()
    {
        $firstname = [
            'Johnathon',
            'Anthony',
            'Erasmo',
            'Raleigh',
            'Nancie',
            'Tama',
            'Camellia',
            'Augustine',
            'Christeen',
            'Luz',
            'Diego',
            'Lyndia',
            'Thomas',
            'Georgianna',
            'Leigha',
            'Alejandro',
            'Marquis',
            'Joan',
            'Stephania',
            'Elroy',
            'Zonia',
            'Buffy',
            'Sharie',
            'Blythe',
            'Gaylene',
            'Elida',
            'Randy',
            'Margarete',
            'Margarett',
            'Dion',
            'Tomi',
            'Arden',
            'Clora',
            'Laine',
            'Becki',
            'Margherita',
            'Bong',
            'Jeanice',
            'Qiana',
            'Lawanda',
            'Rebecka',
            'Maribel',
            'Tami',
            'Yuri',
            'Michele',
            'Rubi',
            'Larisa',
            'Lloyd',
            'Tyisha',
            'Samatha',
        ];


        return $firstname[rand(0, count($firstname) - 1)];
    }

    protected function randomLastname()
    {
        $lastname = [
            'Mischke',
            'Serna',
            'Pingree',
            'Mcnaught',
            'Pepper',
            'Schildgen',
            'Mongold',
            'Wrona',
            'Geddes',
            'Lanz',
            'Fetzer',
            'Schroeder',
            'Block',
            'Mayoral',
            'Fleishman',
            'Roberie',
            'Latson',
            'Lupo',
            'Motsinger',
            'Drews',
            'Coby',
            'Redner',
            'Culton',
            'Howe',
            'Stoval',
            'Michaud',
            'Mote',
            'Menjivar',
            'Wiers',
            'Paris',
            'Grisby',
            'Noren',
            'Damron',
            'Kazmierczak',
            'Haslett',
            'Guillemette',
            'Buresh',
            'Center',
            'Kucera',
            'Catt',
            'Badon',
            'Grumbles',
            'Antes',
            'Byron',
            'Volkman',
            'Klemp',
            'Pekar',
            'Pecora',
            'Schewe',
            'Ramage',
        ];

        return $lastname[rand(0, count($lastname) - 1)];
    }

    protected function randomAge()
    {
        return rand(1, 110);
    }

    protected function randomStars()
    {
        return floatval(rand(1, 5) . '.' . rand(0, 9));
    }
}