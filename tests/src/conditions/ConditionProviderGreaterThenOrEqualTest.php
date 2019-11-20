<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderGreaterThenOrEqualTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testGreaterThenOrEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isGreaterThanOrEqual($value, $comparable);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                false,
                10,
                11
            ],
            [
                true,
                null,
                null
            ],
            [
                false,
                "heinz",
                10
            ],
            [
                true,
                "heinz",
                "Heinz"
            ],
            [
                false,
                "Heinz",
                "heinz"
            ],
            [
                true,
                "Heinz",
                "Heinz"
            ],
            [
                true,
                [],
                []
            ],
            [
                true,
                11,
                10
            ],
            [
                true,
                10,
                10
            ],
            [
                true,
                11.5,
                11.4
            ],
            [
                true,
                11.4,
                11.4
            ],
            [
                false,
                11.4,
                11.5
            ],
            [
                true,
                new \DateTime("2020-01-01"),
                new \DateTime("2020-01-01")
            ],
            [
                true,
                "2020-01-01 00:00:00",
                new \DateTime("2020-01-01")
            ],
            [
                true,
                new \DateTime("2020-01-02"),
                new \DateTime("2020-01-01")
            ],
        ];
    }
}
