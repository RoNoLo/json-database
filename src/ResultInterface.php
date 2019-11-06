<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;

interface ResultInterface
{
    /**
     * How many documents are in the ResultSet.
     *
     * @return int
     */
    public function count(): int;

    /**
     * How many documents were there (before limit or skip was apply'ed)
     *
     * @return int
     */
    public function total(): int;

    /**
     * Returns the first or the only one document.
     *
     * @return array|\stdClass
     */
    public function first();

    /**
     * Returns an document with an ID from the result set.
     *
     * @param string $id
     * @return array|\stdClass
     * @throws DocumentNotFoundException
     */
    public function document($id);

    /**
     * Returns all documents.
     *
     * @return DocumentIterator
     */
    public function all();
}