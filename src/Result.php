<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Exception\{DocumentNotFoundException, ResultSetException};

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 */
abstract class Result implements \IteratorAggregate, \ArrayAccess, \JsonSerializable, \Countable
{
    protected $ids;

    protected $total;

    protected $assoc = false;

    protected $fields = [];

    /**
     * Constructor
     *
     * @param array $ids
     * @param array $fields
     * @param int $total
     * @param bool $assoc
     */
    public function __construct(array $ids = [], array $fields = [], int $total = 0,  bool $assoc = false)
    {
        $this->ids = $ids;
        $this->total = $total;
        $this->assoc = $assoc;
        $this->fields = $fields;
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
    abstract public function document($id);

    /** @return DocumentIterator */
    abstract public function getIterator();

    abstract public function offsetGet($offset);

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    public function offsetExists($offset)
    {
        return isset($this->ids[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        throw new ResultSetException("It is not possible to write to the result set.");
    }

    public function offsetUnset($offset)
    {
        unset($this->ids[$offset]);
    }

    public function jsonSerialize()
    {
        return [
            'ids' => $this->ids,
            'fields' => $this->fields,
            'total' => $this->total,
            'assoc' => $this->assoc
        ];
    }
}