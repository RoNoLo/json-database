<?php

namespace RoNoLo\Flydb;

use RoNoLo\Flydb\Exception\DocumentNotFoundException;
use RoNoLo\Flydb\Exception\DocumentNotStoredException;

interface StoreInterface
{
    /**
     * Stores a document or data structure to the store.
     *
     * @param \stdClass|array $document
     * @return string
     * @throws DocumentNotStoredException
     */
    public function store($document): string;

    /**
     * Reads a document from the store.
     *
     * @param string $id
     * @param bool $assoc
     * @return mixed
     * @throws DocumentNotFoundException
     */
    public function read(string $id, $assoc = false);

    /**
     * Removes a document from the store.
     *
     * @param string $id
     * @return bool
     */
    public function remove(string $id);

    /**
     * Removes many documents from the store.
     *
     * @param array $ids
     * @return mixed
     */
    public function removeMany(array $ids);

    /**
     * @param Query $query
     * @return mixed
     */
    public function find(Query $query): Result;
}
