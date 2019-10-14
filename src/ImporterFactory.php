<?php

namespace RoNoLo\Flydb;

class ImporterFactory
{
    private $repo;

    /**
     * ImporterFactory constructor.
     */
    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
    }
}
