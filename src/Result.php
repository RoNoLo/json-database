<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\ResultSetException;

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 */
class Result implements \IteratorAggregate, \ArrayAccess
{
    protected $store;

    protected $ids;

    protected $total;

    /** @var Query */
    protected $query;

    protected $assoc = false;

    protected $idx = 0;

    /**
     * Constructor
     *
     * @param StoreInterface $store
     * @param array $ids
     * @param int $total
     * @param Query $query
     * @param bool $assoc
     */
    public function __construct(StoreInterface $store, Query $query, array $ids = [], int $total = 0,  bool $assoc = false)
    {
        $this->store = $store;
        $this->ids = $ids;
        $this->total = $total;
        $this->query = $query;
        $this->assoc = $assoc;
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
        return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
    }

    /** @return DocumentIterator */
    public function getIterator()
    {
        return new DocumentIterator($this->store, $this->ids, $this->query->fields(), $this->assoc);
    }

    public function offsetExists($offset)
    {
        return isset($this->ids[$offset]);
    }

    public function offsetGet($offset)
    {
        $id = [$this->ids[$offset]];
        return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
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