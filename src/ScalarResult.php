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
class ScalarResult implements ResultInterface
{
    private $scalar;

    /**
     * Constructor
     *
     * @param $data
     */
    public function __construct($data = null)
    {
        $this->scalar = $data;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return is_null($this->scalar) ? 0 : 1;
    }

    /**
     * @inheritDoc
     */
    public function total(): int
    {
        return is_null($this->scalar) ? 0 : 1;
    }

    /**
     * @inheritDoc
     */
    public function data()
    {
        return $this->scalar;
    }
}
