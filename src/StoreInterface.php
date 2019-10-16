<?php

namespace RoNoLo\Flydb;

use RoNoLo\Flydb\Exception\DocumentNotFoundException;
use RoNoLo\Flydb\Exception\DocumentNotStoredException;

interface StoreInterface
{
    /**
     * Stores a document or data structure to the repository.
     *
     * @param \stdClass|array $document
     * @return string
     * @throws DocumentNotStoredException
     */
    public function store($document): string;

    /**
     * Reads a document from the repository.
     *
     * @param string $id
     * @return mixed
     * @throws DocumentNotFoundException
     */
    public function read(string $id);

    /**
     * Removes a document from the repository.
     *
     * @param string $id
     * @return bool
     */
    public function remove(string $id);

    /**
     * @param Query $query
     * @return mixed
     */
    public function find(Query $query): Result;
}
