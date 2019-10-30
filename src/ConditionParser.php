<?php

namespace RoNoLo\JsonDatabase;

class ConditionParser
{
    protected static $logic = [
        '$or' => Query::LOGIC_OR,
        '$and' => Query::LOGIC_AND,
        '$not' => Query::LOGIC_NOT,
    ];

    public function parse(array $input)
    {
        return $this->parseSelectors($input);
    }

    private function parseSelectors(array &$input, $deep = 0, $context = Query::LOGIC_AND)
    {
        $list = [];
        foreach ($input as $key => $value) {
            if ($key === '$or') {
                $tmpList = [];
                foreach ($value as $i => $subCondition) {
                    $tmpList[] = $this->parseSelectors($subCondition, $deep + 1, self::$logic[$key]);
                }

                $list[] = [
                    self::$logic[$key] => $tmpList
                ];
            } else {
                if (is_array($value)) {
                    foreach ($value as $op => $val) {
                        $list[] = [$op, $key, $val];
                    }
                } else {
                    $list[] = ['$eq', $key, $value];
                }
            }
        }

        return $list;
    }
}
