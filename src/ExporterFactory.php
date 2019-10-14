<?php


namespace RoNoLo\Flydb;


class ExporterFactory
{
    private $repo;

    /**
     * ExporterFactory constructor.
     * @param Repository $repo
     */
    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
    }
}
