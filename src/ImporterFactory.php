<?php

namespace RoNoLo\Flydb;

use RoNoLo\Flydb\Format\FormatInterface;
use RoNoLo\Flydb\Format\JsonFormat;

class ImporterFactory
{
    private $repository;

    /** @var FormatInterface[] */
    private $defaultFormats;

    /**
     * ImporterFactory constructor.
     * @param Store $repository
     */
    public function __construct(Store $repository)
    {
        $this->repository = $repository;

        $this->registerDefaultImporters();
    }

    public function forFormat($format): Importer
    {
        $importer = new Importer($this->repository);
        $importer->setFormat($this->findFormat($format));

        return $importer;
    }

    protected function registerDefaultImporters()
    {
        $this->registerDefaultImporter(JsonFormat::class);
    }

    private function findFormat($format)
    {
        foreach ($this->defaultFormats as $formatClass) {
            if ($formatClass === $format) {
                return new $formatClass();
            }
        }
    }

    private function registerDefaultImporter($format)
    {
        $this->defaultFormats[] = $format;
    }
}
