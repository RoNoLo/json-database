<?php

namespace RoNoLo\JsonStorage\Database\Index;

use RoNoLo\JsonStorage\Exception\QuerySyntaxException;
use RoNoLo\JsonQuery\JsonQuery;

class QueryFields
{
    const OP_AND = '$and';
    const OP_OR = '$or';
    const OP_NOT = '$not';

    const OPERATORS = [
        self::OP_AND,
        self::OP_OR,
        self::OP_NOT
    ];

    public function parse(array $query = [])
    {
        // Check if no selector was given
        if (!count($query)) {
            return [];
        }

        return $this->parseSelectors($query);
    }

    protected function parseSelectors($selectors)
    {
        // Do we have AND condition?
        if ($this->isJsonObject($selectors)) {
            $list = $this->parseAndCondition($selectors);
        } elseif ($this->isJsonArray($selectors)) {
            $list = $this->parseOrCondition($selectors);
        } else {
            return [];
        }

        return $list;
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
        return $this->parseSelectors($selectors);
    }

    private function parseAndCondition(array $selectors)
    {
        $list = [];
        foreach ($selectors as $mixed => $args) {
            if ($this->isOperator($mixed)) {
                $list = array_merge($list, $this->parseOperator($mixed, $args));
            } elseif ($this->isField($mixed)) {
                $list[] = $mixed;
            }
        }

        return $list;
    }

    private function parseOrCondition($selectors)
    {
        $list = [];
        foreach ($selectors as $condition) {
            $list = array_merge($list, $this->parseSelectors($condition));
        }

        $list = array_unique($list);

        return $list;
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