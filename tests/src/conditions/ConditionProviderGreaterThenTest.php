<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderGreaterThenTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testGreaterThen($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isGreaterThan($value, $comparable);

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
                false,
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
                true,
                11,
                10
            ],
            [
                true,
                11.5,
                11.4
            ],
            [
                false,
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
                true,
                new \DateTime("2020-01-02"),
                new \DateTime("2020-01-01")
            ],
        ];
    }
}
