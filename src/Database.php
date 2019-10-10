<?php

namespace RoNoLo\Flydb;

class Database
{
    private $repositories;

    private $indicies;

    public function __construct(...$repositories)
    {
        $this->repositories = $repositories;
    }
}