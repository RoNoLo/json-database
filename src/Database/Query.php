<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;
use RoNoLo\JsonStorage\Database\QueryFields;
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

    /** @var array */
    protected $findFields = [];

    /** @var array */
    protected $usedFields = [];

    /** @var null|string */
    protected $queryFindString = null;

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

        $this->queryFindString = json_encode($input);
        $this->findFields = $this->usedFields = $this->parseUsedFields($input);

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
        $ids = [];
        if ($this->database->hasQueryCache()) {
            $ids = $this->database->findQueryCache($this->storeName, $this->queryFindString);

            if (!is_null($ids)) {
                return $this->postprocess($ids, $assoc, true);
            }
        }

        foreach ($this->database->documentsGenerator($this->storeName, $this->usedFields) as $documentJson) {
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

        return $this->postprocess($ids, $assoc);
    }

    private function postprocess(array $ids = [], bool $assoc = false, bool $fromCache = false)
    {
        $total = count($ids);

        if (!count($ids)) {
            return new Result($this->database, $this->storeName, $ids, $this->fields, $total, $assoc);
        }

        // Query Cache
        if (!$fromCache && $this->database->hasQueryCache()) {
            $this->database->putQueryCache($this->storeName, $this->queryFindString, $ids);
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
                return new Result($this->database, $this->storeName, $ids, $this->fields, $total, $assoc);
            } else {
                $ids = array_slice($ids, $this->skip);
            }
        }

        // Check for 'limit'
        if ($this->limit < count($ids)) {
            $ids = array_slice($ids, 0, $this->limit);
        }

        return new Result($this->database, $this->storeName, $ids, $this->fields, $total, $assoc);
    }

    protected function parseUsedFields(array $input)
    {
        return (new QueryFields())->parse($input);
    }
}
