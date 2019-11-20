<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderEmptyTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testIsEmpty($expected, $value)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isEmpty($value);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                false,
                10,
            ],
            [
                false,
                null,
            ],
            [
                false,
                "heinz",
            ],
            [
                false,
                "heinz",
            ],
            [
                false,
                "Heinz",
            ],
            [
                true,
                [],
            ],
            [
                false,
                11,
            ],
            [
                true,
                '',
            ],
            [
                true,
                "",
            ],
            [
                false,
                11.5,
            ],
            [
                true,
                "\0",
            ],
            [
                false,
                new \stdClass(),
            ],
            [
                false,
                "2020-01-01 00:00:00",
            ],
            [
                false,
                new \DateTime("2020-01-02"),
            ],
        ];
    }
}
