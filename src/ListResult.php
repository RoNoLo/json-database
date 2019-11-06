<?php

namespace RoNoLo\JsonDatabase;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use Traversable;

/**
 * Result
 *
 * A collection of Documents returned from a Query.
 *
 * This class acts like an array but also has some helper methods for
 * manipulating the collection in useful ways.
 */
class ListResult extends Result implements IteratorAggregate
{
    /**
     * Returns the first or the only one document.
     *
     * @return array|\stdClass
     * @throws DocumentNotFoundException
     */
    public function first()
    {
        if (!count($this->ids)) {
            throw new DocumentNotFoundException("The result set had no documents.");
        }

        $id = [$this->ids[0]];
        return (new DocumentIterator($this->store, $id, $this->query->fields(), $this->assoc))->current();
    }

    /**
     * @return DocumentIterator
     */
    public function getIterator()
    {
        return $this->all();
    }
}
