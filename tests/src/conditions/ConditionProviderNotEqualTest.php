<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderNotEqualTest extends TestBase
{
    /**
     * @dataProvider notEqualProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testNotEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isNotEqual($value, $comparable);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function notEqualProvider()
    {
        return [
            [
                false,
                10,
                10
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
                true,
                "heinz",
                "Heinz"
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
