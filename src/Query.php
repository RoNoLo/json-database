<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonQuery\JsonQuery;

/**
 * Query
 *
 * Builds an executes a query whichs searches and sorts documents from a
 * repository.
 *
 * @todo turn the limit and order by arrays into value objects
 */
class Query
{
    const LOGIC_AND = 'AND';
    const LOGIC_OR = 'OR';
    const LOGIC_NOT = 'NOT';

    protected $repo;

    protected $conditions;

    protected $fields;

    protected $skip;

    protected $limit;

    protected $sort;

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

    public function __construct(Store $repository)
    {
        $this->repo = $repository;
        $this->conditionExecutor = new ConditionExecutor();
    }

    public function find(array $input)
    {
        $this->parseInput($input);

        return $this;
    }

    public function execute()
    {
        return $this->repo->find($this);
    }

    public function match($json)
    {
        $q = JsonQuery::fromJson($json);

        return $this->executeConditions($q, $this->conditions);
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    private function executeConditions($q, $conditions)
    {
        foreach ($conditions as $condition) {
            if (is_array($condition) && isset($condition[Query::LOGIC_OR])) {
                foreach ($condition[Query::LOGIC_OR] as $j => $orCondition) {
                    // On OR the first FALSE aborts further checks
                    if ($this->executeConditions($q, $orCondition)) {
                        return true;
                    }
                }

                return false;
            } else {
                $method = self::$rulesMap[$condition[0]];
                $fieldValue = $q->getNestedProperty($condition[1]);
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
