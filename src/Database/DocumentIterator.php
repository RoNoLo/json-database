<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;
use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\DocumentIterator as AbstractDocumentIterator;

class DocumentIterator extends AbstractDocumentIterator
{
    /** @var Database */
    private $database;

    /** @var string */
    private $storeName;

    /**
     * DocumentIterator constructor.
     *
     * @param Database $database
     * @param string $storeName
     * @param array $ids
     * @param array $fields
     * @param bool $assoc
     */
    public function __construct(Database $database, string $storeName, array &$ids, array $fields = [], $assoc = false)
    {
        $this->store = $database;
        $this->storeName = $storeName;

        parent::__construct($ids, $fields, $assoc);
    }

    /**
     * Return the current element
     */
    public function current()
    {
        $document = $this->database->read($this->storeName, $this->ids[$this->idx]);

        return $this->reduceFields($document);
    }
}
