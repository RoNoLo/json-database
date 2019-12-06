<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;
use RoNoLo\JsonStorage\Database\Index\QueryFields;
use RoNoLo\JsonStorage\Exception\QueryExecutionException;
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
    /** @var Database */
    protected $database;

    protected $useIndex = [];

    protected $usedFields = [];

    /** @var string|null */
    protected $storeName = null;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function from(string $storeName)
    {
        $this->storeName = $storeName;

        return $this;
    }

    public function find(array $input)
    {
        parent::find($input);

        $this->parseUsedFields($input);

        return $this;
    }

    public function sort(string $field = null, $direction = "asc")
    {
        parent::sort($field, $direction);

        $this->usedFields = array_merge($this->usedFields, [$field]);

        return $this;
    }

    public function execute($assoc = false)
    {
//        // Check if we can use an index by just executing the query on an index element.
//        if (isset($this->useIndex[$this->store])) {
//            $indexDocuments = $this->database->getIndexMeta($this->store, $this->useIndex[$this->store]);
//            unset($indexDocuments['__id']);
//
//            foreach ($indexDocuments as $id => $indexDocument) {
//                $jsonQuery = JsonQuery::fromData($indexDocument);
//
//                if ($this->match($jsonQuery)) {
//                    if (!$this->sort) {
//                        $ids[$id] = 1;
//                    } else {
//                        $sortField = key($this->sort);
//                        $sortValue = $jsonQuery->get($sortField);
//
//                        if (is_array($sortValue)) {
//                            throw new QueryExecutionException("The field to sort by returned more than one value from a document.");
//                        }
//
//                        $ids[$id] = $sortValue;
//                    }
//                }
//            }
//        } else {
            // No usable index found, we request all documents to perform the query.
            $ids = [];
            foreach ($this->database->documentsGenerator($this->store) as $documentJson) {
                $document = json_decode($documentJson);

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
//        }

        return $this->postprocess($ids, $assoc);
    }

    private function postprocess(array $ids = [], bool $assoc = false)
    {
        $total = count($ids);

        if (!count($ids)) {
            return new Result($this->database, $this->store, $ids, $this->fields, $total, $assoc);
        }

        // Check for sorting
        if ($this->sort) {
            $sortDirection = strtolower(current($this->sort));

            $sortDirection == "asc" ? asort($ids) : arsort($ids);
        }

        $ids = array_keys($ids);

        // Check for 'skip'
        if ($this->skip > 0) {
            if ($this->skip > $total) {
                return new Result($this->database, $this->store, $ids, $this->fields, $total, $assoc);
            } else {
                $ids = array_slice($ids, $this->skip);
            }
        }

        // Check for 'limit'
        if ($this->limit < count($ids)) {
            $ids = array_slice($ids, 0, $this->limit);
        }

        return new Result($this->database, $this->store, $ids, $this->fields, $total, $assoc);
    }

    protected function parseUsedFields(array $input)
    {
        $usedFields = (new QueryFields())->parse($input);

        $this->usedFields = array_merge($this->usedFields, $usedFields);
    }
}
