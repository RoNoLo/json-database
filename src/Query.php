<?php

namespace RoNoLo\Flydb;

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

    public function __construct(Repository $repository)
    {
        $this->repo = $repository;
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

        foreach ($this->conditions as $field => $conditions) {
            foreach ($conditions as $op => $asserts) {
                $method = self::$rulesMap[$op];
                $value = $q->getNestedProperty($field);
                if (!Condition::$method($value, $asserts)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Figuring out what are conditions and what are further
     * processing.
     *
     * @param array $input
     */
    private function parseInput(array $input)
    {
        // Check if we have a very basic query
        if (isset($input["conditions"])) {
            $this->conditions = (new ConditionParser())->parse($input["conditions"]);
        } else {
            $this->conditions = (new ConditionParser())->parse($input);
        }

        // Check if we have a pure AND query without
    }
}
