<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\QuerySyntaxException;
use RoNoLo\JsonQuery\JsonQuery;
use function foo\func;

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

    public function parse(array $query): \Closure
    {
        $this->queryOriginal = json_decode(json_encode($query));

        return $this->buildExecutionTree();
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

    private function parseOperator(string $operator, $selectors)
    {
        switch ($operator) {
            case '$not':
                return $this->parseNotCondition($selectors);

            case '$or':
                return $this->parseOrCondition($selectors);

            case '$and':
                return $this->parseAndCondition($selectors);

            default:
                throw new QuerySyntaxException(sprintf("Unknown $-Operator `%s` found.", $operator));
        }
    }

    private function parseNotCondition($selectors)
    {
        $conditions = $this->parseSelectors($selectors);

        return function (JsonQuery $jsonQuery) use ($conditions) {
            return !$conditions($jsonQuery);
        };
    }

    private function parseAndCondition($selectors)
    {
        $selectorsArray = (array) $selectors;

        $list = [];
        foreach ($selectorsArray as $mixed => $args) {
            if ($this->isOperator($mixed)) {
                $list[] = $this->parseOperator($mixed, $args);
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

        return function (JsonQuery $jsonQuery) use ($list) {
            foreach ($list as $condition) {
                $result = $condition($jsonQuery);

                // AND means the first bool FALSE will abort further checks
                if (!$result) {
                    return false;
                }
            }

            return true;
        };
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

        if (in_array($op, array_keys(ConditionProvider::RULES))) {
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