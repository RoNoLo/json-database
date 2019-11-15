<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\DocumentNotStoredException;

interface DatabaseInterface
{
    /**
     * Stores many documents to the store.
     *
     * @param string $store
     * @param array $documents
     * @return array Of IDs
     */
    public function putMany(string $store, array $documents): array;

    /**
     * Stores a document or data structure to the store.
     *
     * It has to be a single document i.e. a \stdClass after converting it via json_encode().
     *
     * @param string $store
     * @param \stdClass|array $document
     * @return string
     */
    public function put(string $store, $document): string;

    /**
     * Reads a document from the store.
     *
     * @param string $store
     * @param string $id
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     * @return \stdClass|array
     */
    public function read(string $store, string $id, $assoc = false);

    /**
     * Reads documents from the store.
     *
     * @param string $store
     * @param array $ids
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     * @param bool $check If false, no documents exists check will be executed in advance, just the Iterator will be created.
     * @return DocumentIterator
     */
    public function readMany(string $store, array $ids, $assoc = false, $check = true);

    /**
     * Removes a document from the store.
     *
     * @param string $store
     * @param string $id
     * @return bool
     */
    public function remove(string $store, string $id);

    /**
     * Removes many documents from the store.
     *
     * @param string $store
     * @param array $ids
     * @return void
     */
    public function removeMany(string $store, array $ids);
}
