<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderLessThenTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testLessThen($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isLessThan($value, $comparable);

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
                false,
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
                false,
                "Heinz",
                "Heinz"
            ],
            [
                false,
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
                11.4,
                11.5
            ],
            [
                false,
                new \DateTime("2020-01-01"),
                new \DateTime("2020-01-01")
            ],
            [
                false,
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
