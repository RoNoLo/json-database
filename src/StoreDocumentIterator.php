<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonQuery\JsonQuery;

class StoreDocumentIterator implements \Iterator
{
    /** @var DocumentReaderInterface */
    private $documentReader;

    /** @var array */
    private $ids;

    /** @var int */
    private $idx = 0;

    /** @var array */
    private $fields;

    /** @var bool */
    private $assoc;

    /**
     * DocumentIterator constructor.
     *
     * @param DocumentReaderInterface $documentReader
     * @param array $ids
     * @param array $fields
     * @param bool $assoc
     */
    public function __construct(DocumentReaderInterface $documentReader, array &$ids, array $fields = [], $assoc = false)
    {
        $this->documentReader = $documentReader;
        $this->ids = $ids;
        $this->fields = $fields;
        $this->assoc = $assoc;
    }

    /**
     * Return the current element
     */
    public function current()
    {
        $document = $this->documentReader->read($this->ids[$this->idx], false, );

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
}
