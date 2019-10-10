<?php

namespace RoNoLo\Flydb;

class DocumentCollection implements \IteratorAggregate, \Countable
{
    protected $collection = [];

    private function __construct()
    {
        $this->collection = [];
    }

    public function add(Document $document)
    {
        $this->collection[$document->getId()] = $document;
    }

    public static function create()
    {
        return new static();
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }

    public function count()
    {
        return count($this->collection);
    }
}