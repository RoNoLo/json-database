<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 */
abstract class Result
{
    protected $store;

    protected $ids;

    protected $total;

    /** @var Query */
    protected $query;

    protected $assoc = false;

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

    /**
     * Returns all documents.
     *
     * @return DocumentIterator
     */
    public function all()
    {
        return new DocumentIterator($this->store, $this->ids, $this->query->fields(), $this->assoc);
    }
}