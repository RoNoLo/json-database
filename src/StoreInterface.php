<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\DocumentNotStoredException;

interface StoreInterface
{
    /**
     * Tells if a document exists.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Stores many documents to the store.
     *
     * @param array $documents
     *
     * @return array Of IDs
     */
    public function putMany(array $documents): array;

    /**
     * Stores a document or data structure to the store.
     *
     * It has to be a single document i.e. a \stdClass after converting it via json_encode().
     *
     * @param \stdClass|array $document
     *
     * @return string
     * @throws DocumentNotStoredException
     */
    public function put($document): string;

    /**
     * Reads documents from the store.
     *
     * @param array $ids
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     * @param bool $check If false, no documents exists check will be executed in advance, just the Iterator will be created.
     *
     * @return StoreDocumentIterator
     */
    public function readMany(array $ids, $assoc = false, $check = true);

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
     * Removes all documents from the store.
     *
     * @return void
     */
    public function truncate();
}
