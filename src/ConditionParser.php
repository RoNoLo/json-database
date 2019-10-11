<?php

namespace RoNoLo\Flydb;

class ConditionParser
{
    protected static $logic = [
        '$or' => Query::LOGIC_OR,
        '$and' => Query::LOGIC_AND,
    ];

    public function parse(array $input)
    {
        return $this->parseSelectors($input);
    }

    private function rewriteToEqualSyntax(array &$input)
    {
        $list = [];
        foreach ($input as $field => $value) {
            $list[] = ['$eq' => [$field => $value]];
        }

        return $list;
    }

    private function parseSelectors(array &$input)
    {
        $list = [];
        foreach ($input as $key => $value) {
            if (in_array($key, ['$and', '$or'])) {
                foreach ($value as $i => $subCondition) {
                    $list[self::$logic[$key]][] = $this->parseSelectors($subCondition);
                }
            } else {
                if (is_array($value)) {
                    foreach ($value as $op => $val) {
                        $list[Query::LOGIC_AND][] = [$op => [$key => $val]];
                    }
                } else {
                    $list[Query::LOGIC_AND][] = ['$eq' => [$key => $value]];
                }
            }
        }

        return $list;
    }
}