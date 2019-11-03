<?php

namespace RoNoLo\JsonDatabase;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 *
 * This class acts like an array but also has some helper methods for
 * manipulating the collection in useful ways.
 */
class ListResult implements ResultInterface
{
    protected $store;

    protected $ids;

    protected $total;

    protected $fields = [];

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
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->ids);
    }

    /**
     * @inheritDoc
     */
    public function total(): int
    {
        return $this->total;
    }

    /**
     * @inheritDoc
     */
    public function data(?int $idx = null)
    {
        // Requesting a single document?
        if (is_int($idx) && $idx >= 0 && $idx < $this->count()) {
            $id = [$this->ids[$idx]];
            return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
        }

        return new DocumentIterator($this->store, $this->ids, $this->query->fields(), $this->assoc);
    }
}
