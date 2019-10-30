<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\DocumentNotStoredException;

interface StoreInterface
{
    /**
     * Stores many documents to the store.
     *
     * @param array $documents
     * @return void
     */
    public function putMany(array $documents);

    /**
     * Stores a document or data structure to the store.
     *
     * @param \stdClass|array $document
     * @return string
     * @throws DocumentNotStoredException
     */
    public function put($document): string;

    /**
     * Reads a document from the store.
     *
     * @param string $id
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     * @return \stdClass|array
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
     * @return void
     */
    public function removeMany(array $ids);

    /**
     * @param Query $query
     * @return Result
     */
    public function find(Query $query): Result;
}
