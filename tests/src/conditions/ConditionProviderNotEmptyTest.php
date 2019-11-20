<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderNotEmptyTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testNotEmptyThen($expected, $value)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isNotEmpty($value);

        $result = $condition();

        $this->assertEquals($expected, $result);
    }

    public function equalProvider()
    {
        return [
            [
                true,
                10,
            ],
            [
                true,
                null,
            ],
            [
                true,
                "heinz",
            ],
            [
                true,
                "heinz",
            ],
            [
                true,
                "Heinz",
            ],
            [
                false,
                [],
            ],
            [
                true,
                11,
            ],
            [
                false,
                '',
            ],
            [
                false,
                "",
            ],
            [
                true,
                11.5,
            ],
            [
                false,
                "\0",
            ],
            [
                true,
                new \stdClass(),
            ],
            [
                true,
                "2020-01-01 00:00:00",
            ],
            [
                true,
                new \DateTime("2020-01-02"),
            ],
        ];
    }
}
