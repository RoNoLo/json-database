<?php

namespace RoNoLo\JsonStorage\Store;

use RoNoLo\JsonStorage\Exception\QueryExecutionException;
use RoNoLo\JsonStorage\Store;
use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\Query as AbstractQuery;

/**
 * Query
 *
 * Builds an executes a query which searches and sorts documents from a
 * repository.
 */
class Query extends AbstractQuery
{
    /** @var Store */
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function execute($assoc = false)
    {
        $ids = [];
        foreach ($this->store->documentsGenerator() as $documentJson) {
            $document = json_decode($documentJson);

            // Done here to reuse it for sorting
            $jsonQuery = JsonQuery::fromData($document);

            if ($this->match($jsonQuery)) {
                if (!$this->sort) {
                    $ids[$document->__id] = 1;
                } else {
                    $sortField = key($this->sort);
                    $sortValue = $jsonQuery->get($sortField);

                    if (is_array($sortValue)) {
                        throw new QueryExecutionException("The field to sort by returned more than one value from a document.");
                    }

                    $ids[$document->__id] = $sortValue;
                }
            }
        }

        // Check for sorting
        if ($this->sort) {
            $sortDirection = strtolower(current($this->sort));

            $sortDirection == "asc" ? asort($ids) : arsort($ids);
        }

        $ids = array_keys($ids);

        $total = count($ids);

        // Check for 'skip'
        if ($this->skip > 0) {
            if ($this->skip > $total) {
                return new Result($this->store, $ids, $this->fields, $total, $assoc);
            } else {
                $ids = array_slice($ids, $this->skip);
            }
        }

        // Check for 'limit'
        if ($this->limit < count($ids)) {
            $ids = array_slice($ids, 0, $this->limit);
        }

        return new Result($this->store, $ids, $this->fields, $total, $assoc);
    }
}
