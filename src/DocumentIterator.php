<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonQuery\JsonQuery;

abstract class DocumentIterator implements \Iterator
{
    /** @var array */
    protected $ids;

    /** @var int */
    protected $idx = 0;

    /** @var array */
    protected $fields;

    /** @var bool */
    protected $assoc;

    /**
     * DocumentIterator constructor.
     *
     * @param array $ids
     * @param array $fields
     * @param bool $assoc
     */
    public function __construct(array &$ids, array $fields = [], $assoc = false)
    {
        $this->ids = $ids;
        $this->fields = $fields;
        $this->assoc = $assoc;
    }

    /**
     * Return the current element
     */
    abstract public function current();

    /**
     * Move forward to next element.
     */
    public function next()
    {
        $this->idx++;
    }

    /**
     * Return the key of the current element.
     */
    public function key()
    {
        return $this->ids[$this->idx];
    }

    /**
     * Checks if current position is valid.
     */
    public function valid()
    {
        return isset($this->ids[$this->idx]);
    }

    /**
     * Rewind the Iterator to the first element.
     */
    public function rewind()
    {
        $this->idx = 0;
    }

    /**
     * Reduces the document fields, if requested.
     *
     * @param \stdClass|array $document
     *
     * @return \stdClass|array
     */
    protected function reduceFields($document)
    {
        if (!count($this->fields)) {
            return $this->assoc ? json_decode(json_encode($document), true) : $document;
        }

        $jsonQuery = JsonQuery::fromData($document);

        $doc = [];
        foreach ($this->fields as $to => $from) {
            if (is_numeric($to)) {
                $to = $from;
            }

            $doc[$to] = $jsonQuery->get($from);
        }

        return $this->assoc ? $doc : json_decode(json_encode($doc));
    }
}
