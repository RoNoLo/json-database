<?php

namespace RoNoLo\Flydb;

class DocumentIterator implements \Iterator
{
    private $repo;

    private $id;

    private $index = 0;

    /**
     * DocumentIterator constructor.
     * @param $repo
     * @param $id
     */
    public function __construct(Store $repo, array $id)
    {
        $this->repo = $repo;
        $this->id = $id;
    }

    /**
     * Return the current element
     */
    public function current()
    {
        return $this->repo->read($this->id[$this->index]);
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     */
    public function key()
    {
        return $this->id[$this->index];
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return isset($this->id[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->index = 0;
    }
}
