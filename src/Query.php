<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonQuery\JsonQuery;
use RoNoLo\JsonStorage\Query\QueryExecuter;

/**
 * Query
 *
 * Builds an executes a query which searches and sorts documents from a
 * repository.
 */
abstract class Query
{
    /** @var \Closure */
    protected $conditions = [];

    /** @var array */
    protected $fields = [];

    /** @var int */
    protected $skip = 0;

    /** @var int */
    protected $limit = PHP_INT_MAX;

    /** @var null */
    protected $sort = null;

    /** @var string|null */
    protected $store = null;

    public function from(string $store)
    {
        $this->store = $store;

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

    public function match(JsonQuery $jsonQuery)
    {
        return ($this->conditions)($jsonQuery);
    }

    abstract public function execute($assoc = false);

    /**
     * Parsing the given JSON like query into an execution tree
     * of Closures.
     *
     * @param array $conditions
     */
    protected function parseInput(array $conditions)
    {
        $this->conditions = (new QueryExecuter())->parse($conditions);
    }
}
