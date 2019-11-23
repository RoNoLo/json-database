<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;
use RoNoLo\JsonStorage\Exception\DocumentNotFoundException;
use RoNoLo\JsonStorage\Exception\ResultSetException;
use RoNoLo\JsonStorage\Result as AbstractResult;
use RoNoLo\JsonStorage\Store;

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 */
class Result extends AbstractResult
{
    /** @var Database */
    protected $database;

    /** @var string */
    protected $storeName;

    public static function fromJson(Database $database, string $storeName, $data)
    {
        $data = (array) $data;

        $ids = $data['ids'];
        $fields = $data['fields'];
        $total = $data['total'];
        $assoc = $data['assoc'];

        return new self($database, $storeName, $ids, $fields, $total, $assoc);
    }

    /**
     * Constructor
     *
     * @param Database $database
     * @param string $storeName
     * @param array $ids
     * @param array $fields
     * @param int $total
     * @param bool $assoc
     */
    public function __construct(Database $database, string $storeName, array $ids = [], array $fields = [], int $total = 0,  bool $assoc = false)
    {
        $this->database = $database;
        $this->storeName = $storeName;

        parent::__construct($ids, $fields, $total, $assoc);
    }

    /**
     * How many documents are in the ResultSet.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->ids);
    }

    /**
     * How many documents were there (before limit or skip was apply'ed)
     *
     * @return int
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * Returns an document with an ID from the result set.
     *
     * @param string $id
     * @return array|\stdClass
     * @throws DocumentNotFoundException
     */
    public function document($id)
    {
        if (!in_array($id, $this->ids)) {
            throw new DocumentNotFoundException("No documentwith ID " . $id . " was found in result set.");
        }

        $id = [$id];
        return (new DocumentIterator($this->database, $this->storeName, $id, $this->fields, $this->assoc))->current();
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /** @return DocumentIterator */
    public function getIterator()
    {
        return new DocumentIterator($this->database, $this->storeName, $this->ids, $this->fields, $this->assoc);
    }

    public function offsetExists($offset)
    {
        return isset($this->ids[$offset]);
    }

    public function offsetGet($offset)
    {
        $id = [$this->ids[$offset]];
        return (new DocumentIterator($this->database, $this->storeName, $id, $this->fields, $this->assoc))->current();
    }

    public function offsetSet($offset, $value)
    {
        throw new ResultSetException("It is not possible to write to the result set.");
    }

    public function offsetUnset($offset)
    {
        unset($this->ids[$offset]);
    }
}