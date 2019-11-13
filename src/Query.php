<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\QuerySyntaxException;
use RoNoLo\JsonQuery\JsonQuery;

/**
 * Query
 *
 * Builds an executes a query whichs searches and sorts documents from a
 * repository.
 */
class Query
{
    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';
    const LOGIC_NOT = 'NOT';

    /** @var StoreInterface */
    protected $store = null;

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

    protected static $rulesMap = [
        '$eq' => 'isEqual',
        '$neq' => 'isNotEqual',
        '$gt' => 'isGreaterThan',
        '$lt' => 'isLessThan',
        '$gte' => 'isGreaterThanOrEqual',
        '$lte' => 'isLessThanOrEqual',
        '$in'    => 'isIn',
        '$nin' => 'isNotIn',
        '$null' => 'isNull',
        '$n' => 'isNull',
        '$notnull' => 'isNotNull',
        '$nn' => 'isNotNull',
        '$contains' => 'contains',
        '$c' => 'contains',
        '$ne' => 'isNotEmpty',
        '$e' => 'isEmpty',
        '$not' => 'isNot'
    ];

    protected $conditionExecutor;

    public function __construct(StoreInterface $store)
    {
        $this->store = $store;
        $this->conditionExecutor = new ConditionExecutor();
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
     *
     * @param array $fields
     *
     * @return $this|array
     */
    public function fields(?array $fields = null)
    {
        if (is_null($fields)) {
            return $this->fields;
        }

        $this->fields = $fields;

        return $this;
    }

    public function sort(?string $field = null, $direction = "asc")
    {
        if (is_null($field)) {
            return $this->sort;
        }

        $this->sort = [$field => $direction];

        return $this;
    }

    public function limit(?int $limit = null)
    {
        if (is_null($limit)) {
            return $this->limit;
        }

        $this->limit = $limit;

        return $this;
    }

    public function skip(?int $skip = null)
    {
        if (is_null($skip)) {
            return $this->skip;
        }

        $this->skip = $skip;

        return $this;
    }

    public function execute()
    {
        return $this->store->find($this);
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
