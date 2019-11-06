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

    /** @inheritDoc */
    public function count(): int
    {
        return count($this->ids);
    }

    /** @inheritDoc */
    public function total(): int
    {
        return $this->total;
    }

    /** @inheritDoc */
    public function first()
    {
        if (!count($this->ids)) {
            throw new DocumentNotFoundException("The result set had no documents.");
        }

        $id = [$this->ids[0]];
        return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
    }

    /** @inheritDoc */
    public function document($id)
    {
        if (!in_array($id, $this->ids)) {
            throw new DocumentNotFoundException("No documentwith ID " . $id . " was found in result set.");
        }

        $id = [$id];
        return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
    }

    /** @inheritDoc */
    public function all()
    {
        return new DocumentIterator($this->store, $this->ids, $this->query->fields(), $this->assoc);
    }

    /** @inheritDoc */
    public function data(?int $idx = null)
    {
        if (!isset($this->ids[$idx])) {
            throw new DocumentNotFoundException("No document at index " . $idx . " was found.");
        }

        // Requesting a single document?
        if (is_int($idx) && $idx >= 0 && $idx < $this->count()) {
            $id = [$this->ids[$idx]];
            return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
        }

        return new DocumentIterator($this->store, $this->ids, $this->query->fields(), $this->assoc);
    }
}
