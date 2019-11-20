<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderInTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testIsIn($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isIn($value, $comparable);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                false,
                10,
                [11, 12, 13, 14]
            ],
            [
                false,
                "bernd",
                []
            ],
            [
                false,
                "heinz",
                ["luisa", "jane", "melanie", "antje"]
            ],
            [
                false,
                "heinz",
                ["Heinz"]
            ],
            [
                false,
                null,
                []
            ],
            [
                true,
                "Heinz",
                ["Heinz"]
            ],
            [
                true,
                '1',
                ['1', '123', '45']
            ],
            [
                true,
                11,
                [10, 11, 12, 14]
            ],
        ];
    }
}
