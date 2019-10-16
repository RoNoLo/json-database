<?php

namespace RoNoLo\Flydb;

use RoNoLo\Flydb\Exception\JsonCollectionImportException;
use RoNoLo\Flydb\Format\FormatInterface;

class Importer
{
    /** @var RepositoryInterface */
    private $repository;

    /** @var FormatInterface */
    private $format;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function setFormat(FormatInterface $format)
    {
        $this->format = $format;
    }

    public function import($data, $isCollection = false): Result
    {
        $data = $this->format->import($data);

        if ($isCollection) {
            if (!is_array($data)) {
                throw new JsonCollectionImportException("The imported data was not an array, but was marked as collection");
            }

            foreach ($data as $item) {
                $this->repository->store($item);
            }
        }
    }
}