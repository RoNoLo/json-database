<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderNotInTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testIsNotIn($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isNotIn($value, $comparable);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                true,
                10,
                [11, 12, 13, 14]
            ],
            [
                true,
                "bernd",
                []
            ],
            [
                true,
                "heinz",
                ["luisa", "jane", "melanie", "antje"]
            ],
            [
                true,
                "heinz",
                ["Heinz"]
            ],
            [
                true,
                null,
                []
            ],
            [
                false,
                "Heinz",
                ["Heinz"]
            ],
            [
                false,
                '1',
                ['1', '123', '45']
            ],
            [
                false,
                11,
                [10, 11, 12, 14]
            ],
        ];
    }
}
