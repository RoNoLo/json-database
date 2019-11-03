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

    protected $store;

    protected $conditions;

    protected $fields;

    protected $skip = 0;

    protected $limit = PHP_INT_MAX;

    protected $sort = null;

    protected static $rulesMap = [
        '$eq' => 'equal',
        '$seq' => 'strictEqual',
        '$neq' => 'notEqual',
        '$sneq' => 'strictNotEqual',
        '$gt' => 'greaterThan',
        '$lt' => 'lessThan',
        '$gte' => 'greaterThanOrEqual',
        '$lte' => 'lessThanOrEqual',
        '$in'    => 'in',
        '$nin' => 'notIn',
        '$null' => 'isNull',
        '$n' => 'isNull',
        '$notnull' => 'isNotNull',
        '$nn' => 'isNotNull',
        '$sw' => 'startWith',
        '$ew' => 'endWith',
        '$contains' => 'contains',
        '$c' => 'contains',
        '$ne' => 'isNotEmpty',
        '$e' => 'isEmpty'
    ];

    protected $conditionExecutor;

    public function __construct(Store $store)
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
    public function fields(array $fields = null)
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
        return $this->executeConditions($jsonQuery, $this->conditions);
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    private function executeConditions($jsonQuery, $conditions)
    {
        foreach ($conditions as $condition) {
            if (is_array($condition) && isset($condition[Query::LOGIC_OR])) {
                foreach ($condition[Query::LOGIC_OR] as $j => $orCondition) {
                    // On OR the first FALSE aborts further checks
                    if ($this->executeConditions($jsonQuery, $orCondition)) {
                        return true;
                    }
                }

                return false;
            } else {
                $method = self::$rulesMap[$condition[0]];
                $fieldValue = $jsonQuery->getNestedProperty($condition[1]);
                $value = $condition[2];

                // On AND the first FALSE aborts further checks
                if (!$this->conditionExecutor->$method($fieldValue, $value)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Parsing the given JSON like query into conditions and
     * postprocessing.
     *
     * @param array $input
     */
    private function parseInput(array $input)
    {
        // Check if we have a very basic query
        if (isset($input["conditions"])) {
            $this->conditions = (new ConditionParser())->parse($input["conditions"]);
            $this->fields = isset($input["fields"]) ? $input["fields"] : null;
            $this->skip = isset($input["skip"]) ? $input["skip"] : null;
            $this->sort = isset($input["sort"]) ? $input["sort"] : null;
            $this->limit = isset($input["limit"]) ? $input["limit"] : null;
        } else {
            $this->conditions = (new ConditionParser())->parse($input);
        }
    }
}
