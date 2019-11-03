<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonQuery\JsonQuery;

class DocumentIterator implements \Iterator
{
    private $store;

    private $ids;

    private $index = 0;

    /** @var array */
    private $fields;

    /** @var bool */
    private $assoc;

    /**
     * DocumentIterator constructor.
     * @param StoreInterface $store
     * @param array $ids
     * @param array $fields
     * @param bool $assoc
     */
    public function __construct(StoreInterface $store, array &$ids, array $fields = [], $assoc = false)
    {
        $this->store = $store;
        $this->ids = $ids;
        $this->fields = $fields;
        $this->assoc = $assoc;
    }

    /**
     * Return the current element
     */
    public function current()
    {
        $document = $this->store->read($this->ids[$this->index]);

        if (!count($this->fields)) {
            return $this->assoc ? json_decode(json_encode($document), true) : $document;
        }

        $jsonQuery = JsonQuery::fromData($document);

        $doc = [];
        foreach ($this->fields as $to => $from) {
            if (is_numeric($to)) {
                $to = $from;
            }

            $doc[$to] = $jsonQuery->getNestedProperty($from);
        }

        return $this->assoc ? $doc : json_decode(json_encode($doc));
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
