<?php


namespace RoNoLo\Flydb;


class ExporterFactory
{
    private $repo;

    /**
     * ExporterFactory constructor.
     * @param Store $repo
     */
    public function __construct(Store $repo)
    {
        $this->repo = $repo;
    }
}
