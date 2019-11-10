<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\QuerySyntaxException;
use RoNoLo\JsonQuery\JsonQuery;

class QueryExecuter
{
    protected $queryOriginal = [];

    protected $selectors = [];

    protected static $rulesMap = [
        '$eq' => 'equal',
        '$seq' => 'strictEqual',
        '$neq' => 'notEqual',
        '$sneq' => 'strictNotEqual',
        '$gt' => 'greaterThan',
        '$lt' => 'lessThan',
        '$gte' => 'greaterThanOrEqual',
        '$lte' => 'lessThanOrEqual',
        '$in' => 'in',
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
        '$e' => 'isEmpty',
        '$not' => 'notOperator',
        '$and' => 'andOperator',
        '$or' => 'orOperator'
    ];

    /** @var \Closure */
    private $executionTree;

    public function parse(array $query): \Closure
    {
        $this->queryOriginal = json_decode(json_encode($query));

        return $this->buildExecutionTree();

        // return $this->executionTree;
    }

    public function getSelectors()
    {
        return $this->selectors;
    }

    private function preProcess(array $query)
    {
        $rewrite = [];

        $isSimple = false;

        // First check if the query if of type simple AND conditions
        foreach ($query as $field => $condition) {
            if (is_array($condition) && $isSimple) {
                throw new QuerySyntaxException("You cannot mix the simple query syntax with condition $-op conditions");
            }

            if (is_string($field) && !is_array($condition)) {
                // If the first field/value is a simple one, every has to be one.
                $isSimple = true;

                $rewrite[$field] = ['$eq' => $condition];
            }
        }

        if ($isSimple) {
            $this->selectors = ['$and' => $rewrite];
        } else {
            $this->selectors = $query;
        }
    }

    private function buildExecutionTree()
    {
        return $this->parseSelectors($this->queryOriginal);
    }

    protected function parseSelectors($selectors): \Closure
    {
        // Do we have AND condition?
        if (is_object($selectors)) {
            $conditions = $this->parseAndCondition($selectors);
        } elseif (is_array($selectors)) {
            // Check if no selectors where applyed to just get all documents
            if (!count($selectors)) {
                return function () {
                    return true;
                };
            }

            $conditions = $this->parseOrCondition($selectors);
        } else {
            throw new \Exception("What are you?");
        }

        return $conditions;
    }

    private function parseAndCondition($selectors)
    {
        $tmp = (array) $selectors;

        $list = [];
        foreach ($tmp as $mixed => $args) {
            if ($this->isOperator($mixed)) {
                $foo = 1;
            } elseif ($this->isField($mixed)) {
                if (is_object($args)) {
                    $conditions = (array) $args;

                    foreach ($conditions as $op => $value) {
                        $list[] = function (JsonQuery $jsonQuery) use ($mixed, $value, $op) {
                            $fieldValue = $jsonQuery->get($mixed);

                            return (new ConditionProvider())->get($op, $fieldValue, $value)();
                        };
                    }
                } else {
                    // Simple isEqual
                    $list[] = function (JsonQuery $jsonQuery) use ($mixed, $args) {
                        return $jsonQuery->get($mixed) == $args;
                    };
                }
            }
        }

        $andCloure = function (JsonQuery $jsonQuery) use ($list) {
            foreach ($list as $condition) {
                $result = $condition($jsonQuery);

                // AND means the first bool FALSE will abort further checks
                if (!$result) {
                    return false;
                }
            }

            return true;
        };

        return $andCloure;
    }

    private function parseOrCondition($selectors)
    {
        $list = [];
        foreach ($selectors as $condition) {
            $list[] = $this->parseSelectors($condition);
        }

        $orClosure = function (JsonQuery $jsonQuery) use ($list) {
            foreach ($list as $condition) {
                $result = $condition($jsonQuery);

                // OR means the first bool TRUE will abort further checks
                if ($result) {
                    return true;
                }
            }

            return false;
        };

        return $orClosure;
    }

    private function isOperator(string $op)
    {
        if (strpos($op, '$') !== 0) {
            return false;
        }

        if (in_array($op, array_keys(self::$rulesMap))) {
            return true;
        }

        throw new QuerySyntaxException(sprintf("Unknown $-Operator `%s` found.", $op));
    }

    private function isField($field)
    {
        if (!is_string($field)) {
            return false;
        }

        if (strpos($field, '$') !== false) {
            throw new QuerySyntaxException(sprintf("A field with an $-symbol somewhere was found in `%s`. That is not allowed.", $field));
        }

        return true;
    }
}