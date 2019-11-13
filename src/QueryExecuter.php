<?php

namespace RoNoLo\JsonDatabase;

use RoNoLo\JsonDatabase\Exception\QuerySyntaxException;
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

    const CAST_DATETIME = '$DateTime:';

    const VALUE_CASTS = [
        self::CAST_DATETIME => '/^(\$DateTime:) ?(.+)$/',
    ];

    protected $queryOriginal = [];

    public function parse(array $query): \Closure
    {
        $this->queryOriginal = json_decode(json_encode($query));

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
                        $value = $this->parseValueForTypeCasting($value);
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
     * String values could have a case operator, which will turn it
     * into a specific type. This will be done on "compile time".
     *
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    private function parseValueForTypeCasting($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        foreach (self::VALUE_CASTS as $cast => $regex) {
            if (strpos($value, $cast) === 0 && preg_match($regex, $value, $matches)) {
                switch ($cast) {
                    case $matches[1]: return new \DateTime($matches[2]);
                }
            }
        }

        return $value;
    }
}