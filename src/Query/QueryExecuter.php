<?php

namespace RoNoLo\JsonStorage\Query;

use RoNoLo\JsonStorage\Exception\QuerySyntaxException;
use RoNoLo\JsonQuery\JsonQuery;

class QueryExecuter
{
    const OP_AND = '$and';
    const OP_OR = '$or';
    const OP_NOT = '$not';

    const OPERATORS = [
        self::OP_AND,
        self::OP_OR,
        self::OP_NOT
    ];

    public function parse(array $query = []): \Closure
    {
        // Check if no selector was given
        if (!count($query)) {
            return function () { return true; };
        }

        return $this->parseSelectors($query);
    }

    protected function parseSelectors($selectors): \Closure
    {
        // Do we have AND condition?
        if ($this->isJsonObject($selectors)) {
            $conditions = $this->parseAndCondition($selectors);
        } elseif ($this->isJsonArray($selectors)) {
            $conditions = $this->parseOrCondition($selectors);
        } else {
            // This can be stupid. Will see.
            $conditions = function () { return true; };
        }

        return $conditions;
    }

    private function parseOperator(string $operator, $selectors)
    {
        switch ($operator) {
            case self::OP_NOT:
                return $this->parseNotCondition($selectors);

            case self::OP_OR:
                return $this->parseOrCondition($selectors);

            case self::OP_AND:
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

    private function parseAndCondition(array $selectors)
    {
        $list = [];
        foreach ($selectors as $mixed => $args) {
            if ($this->isOperator($mixed)) {
                $list[] = $this->parseOperator($mixed, $args);
            } elseif ($this->isField($mixed)) {
                if ($this->isJsonObject($args)) {
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

        if (in_array($op, array_keys(self::OPERATORS))) {
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

    /**
     * An AND syntax is defined by the first array key.
     * If it is a string, it's an AND syntax.
     *
     * @param $selectors
     *
     * @return bool
     */
    private function isJsonObject($selectors)
    {
        return is_array($selectors) && is_string(key($selectors));
    }

    /**
     * An OR syntax is defined by the first array key.
     * If it is an int, it's an OR syntax.
     *
     * @param $selectors
     *
     * @return bool
     */
    private function isJsonArray($selectors)
    {
        return is_array($selectors) && is_int(key($selectors));
    }
}