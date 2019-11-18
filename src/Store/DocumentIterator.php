<?php

namespace RoNoLo\JsonStorage\Store;

use RoNoLo\JsonStorage\Store;
use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\DocumentIterator as AbstractDocumentIterator;

class DocumentIterator extends AbstractDocumentIterator
{
    /** @var Store */
    private $store;

    /**
     * DocumentIterator constructor.
     *
     * @param Store $store
     * @param array $ids
     * @param array $fields
     * @param bool $assoc
     */
    public function __construct(Store $store, array &$ids, array $fields = [], $assoc = false)
    {
        $this->store = $store;

        parent::__construct($ids, $fields, $assoc);
    }

    /**
     * Return the current element
     */
    public function current()
    {
        $document = $this->store->read($this->ids[$this->idx], false);

        return $this->reduceFields($document);
    }
}
