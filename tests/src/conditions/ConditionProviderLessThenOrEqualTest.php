<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderLessThenOrEqualTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testLessThenOrEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isLessThanOrEqual($value, $comparable);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                true,
                10,
                11
            ],
            [
                true,
                null,
                null
            ],
            [
                true,
                "heinz",
                10
            ],
            [
                false,
                "heinz",
                "Heinz"
            ],
            [
                true,
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
                false,
                11,
                10
            ],
            [
                false,
                11.5,
                11.4
            ],
            [
                true,
                11.5,
                11.5
            ],
            [
                true,
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
                false,
                new \DateTime("2020-01-02"),
                new \DateTime("2020-01-01")
            ],
            [
                true,
                new \DateTime("2020-01-01"),
                new \DateTime("2020-01-03")
            ],
        ];
    }
}
