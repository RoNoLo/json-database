<?php

namespace RoNoLo\Flydb;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use RoNoLo\Flydb\Exception\DocumentNotFoundException;

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 *
 * This class acts like an array but also has some helper methods for
 * manipulating the collection in useful ways.
 */
class Result implements IteratorAggregate, ArrayAccess, Countable
{
    protected $repo;

    protected $id;

    protected $total;

    /**
     * Constructor
     *
     * @param Repository $repo
     * @param array $ids
     * @param int $total
     */
    public function __construct(Repository $repo, array $id, int $total)
    {
        $this->repo = $repo;
        $this->id = $id;
        $this->total = $total;
    }

    public function export()
    {
        return new ExporterFactory($this->repo);
    }

    /**
     * Returns the number of documents in this result
     *
     * @return integer The number of documents
     */
    public function count()
    {
        return count($this->id);
    }

    /**
     * Returns the total number of documents (if using limit in a query).
     * Useful for working out pagination.
     *
     * @return integer The total number of documents
     */
    public function total()
    {
        return $this->total;
    }

    public function getIds()
    {
        return $this->id;
    }

    public function getIterator()
    {
        return new DocumentIterator($this->repo, $this->id);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception('Cannot set values on Flydb\Result');
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->id[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        throw new Exception('Cannot unset values on Flydb\Result');
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return isset($this->id[$offset]) ? $this->repo->read($this->id[$offset]) : null;
    }

    /**
     * Gets the first document from the result.
     *
     * @return mixed The first document, or false if the result is empty.
     * @throws DocumentNotFoundException
     * @throws \ReflectionException
     */
    public function first()
    {
        return !empty($this->id) && isset($this->id[0]) ? $this->repo->read($this->id[0]) : false;
    }

    /**
     * Gets the last document from the results.
     *
     * @return mixed The last document, or false if the result is empty.
     * @throws DocumentNotFoundException
     * @throws \ReflectionException
     */
    public function last()
    {
        return !empty($this->id) && isset($this->id[count($this->id) - 1]) ? $this->repo->read($this->id[count($this->id) - 1]) : false;
    }
}
