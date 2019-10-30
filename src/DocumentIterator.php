<?php

namespace RoNoLo\JsonDatabase;

class DocumentIterator implements \Iterator
{
    private $store;

    private $ids;

    private $index = 0;

    /** @var array */
    private $fields;

    /**
     * DocumentIterator constructor.
     * @param StoreInterface $store
     * @param array $ids
     * @param array $fields
     */
    public function __construct(StoreInterface $store, array $ids, array $fields = [])
    {
        $this->store = $store;
        $this->ids = $ids;
        $this->fields = $fields;
    }

    /**
     * Return the current element
     */
    public function current()
    {
        $document = $this->store->read($this->ids[$this->index]);

        if (!count($this->fields)) {
            return $document;
        }

        throw new \Exception("Field Reduce not implemented yet.");
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
        return $this->ids[$this->index];
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return isset($this->ids[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->index = 0;
    }
}
