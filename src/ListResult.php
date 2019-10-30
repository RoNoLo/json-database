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

    /**
     * Constructor
     *
     * @param StoreInterface $store
     * @param array $ids
     * @param int $total
     * @param array $fields
     */
    public function __construct(StoreInterface $store, array $ids = [], int $total = 0, array $fields = [])
    {
        $this->store = $store;
        $this->ids = $ids;
        $this->total = $total;
        $this->fields = $fields;
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
    public function data(?int $from = null, ?int $to = null)
    {
        // Requesting a single document?
        if (is_int($from) && is_int($to) && $from >= 0 && $from == $to && $from < $this->count()) {
            return $this->store->read($this->ids[$from]);
        }

        return new DocumentIterator($this->store, $this->ids, $this->fields);
    }
}
