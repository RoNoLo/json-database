<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\QueryExecutionException;
use RoNoLo\JsonQuery\JsonQuery;

/**
 * Query
 *
 * Builds an executes a query whichs searches and sorts documents from a
 * repository.
 */
class Query
{
    /** @var DocumentsGeneratorInterface */
    protected $documentStorage = null;

    /** @var \Closure */
    protected $conditions = [];

    /** @var array */
    protected $fields = [];

    /** @var int */
    protected $skip = 0;

    /** @var int */
    protected $limit = PHP_INT_MAX;

    /** @var string|null */
    protected $fromStore = null;

    /** @var null */
    protected $sort = null;

    public function __construct(DocumentsGeneratorInterface $documentStorage)
    {
        $this->documentStorage = $documentStorage;
    }

    public function from(string $name)
    {
        $this->fromStore = $name;

        return $this;
    }

    public function find(array $input)
    {
        $this->parseInput($input);

        return $this;
    }

    /**
     * Modifies the document on the fly.
     *
     * There are a few syntax options. You can say, which data to keep,
     * which to delete and which to rewrite. The order of calling this method
     * matters. You can call this method more then once. It will processed
     * in that order.
     *
     * To COPY key/values:
     * ->fields(["to" => "from.here", "bernd" => "foo.moo.boo"]);
     * To KEEP only specific keys:
     * ->fields(["name", "age", "color"]);
     *
     * @param array $fields
     *
     * @return $this|array
     */
    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Sets the field to sort by and direction.
     *
     * @param string $field
     * @param string $direction
     *
     * @return $this
     */
    public function sort(string $field = null, $direction = "asc")
    {
        $this->sort = [$field => $direction];

        return $this;
    }

    public function limit(int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function skip(int $skip)
    {
        $this->skip = $skip;

        return $this;
    }

    public function execute($assoc = false)
    {
        $ids = [];
        foreach ($this->documentStorage->generateAllDocuments($this->fromStore) as $documentJson) {
            $document = json_decode($documentJson);

            // Done here to reuse it for sorting
            $jsonQuery = JsonQuery::fromData($document);

            if ($this->match($jsonQuery)) {
                if (!$this->sort()) {
                    $ids[$document->__id] = 1;
                } else {
                    $sortField = key($this->sort());
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
                return new Result($this);
            } else {
                $ids = array_slice($ids, $this->skip);
            }
        }

        // Check for 'limit'
        if ($this->limit < count($ids)) {
            $ids = array_slice($ids, 0, $this->limit);
        }

        return new Result($this->documentStorage, $this, $ids, $total, $assoc);
    }

    public function match(JsonQuery $jsonQuery)
    {
        return ($this->conditions)($jsonQuery);
    }

    /**
     * Parsing the given JSON like query into an execution tree
     * of Closures.
     *
     * @param array $conditions
     */
    private function parseInput(array $conditions)
    {
        $this->conditions = (new QueryExecuter())->parse($conditions);
    }
}
