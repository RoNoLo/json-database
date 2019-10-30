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

    /**
     * Constructor
     *
     * @param StoreInterface $store
     * @param array $ids
     * @param int $total
     */
    public function __construct(StoreInterface $store, array $ids, int $total)
    {
        $this->store = $store;
        $this->ids = $ids;
        $this->total = $total;
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
    public function data()
    {
        $count = $this->count();
        for ($i = 0; $i < $count; $i++) {
            yield $this->store->read($this->ids[$i]);
        }
    }
}
