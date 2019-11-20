<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderEqualTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testIsEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isEqual($value, $comparable);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                true,
                10,
                10
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
                false,
                "heinz",
                "Heinz"
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
        ];
    }
}
