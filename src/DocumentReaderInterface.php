<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;

interface DocumentReaderInterface
{
    /**
     * Returns all documents for further processing (like a query).
     *
     * @param string $id
     * @param bool $assoc Will be used for json_decode's 2nd argument. If null is given a JSON string will be returned.
     * @param string|null $storeName
     *
     * @return \stdClass|array
     * @throws DocumentNotFoundException
     */
    public function read(string $id, $assoc = false, string $storeName = null);
}