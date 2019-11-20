<?php

namespace RoNoLo\JsonStorage;

use RoNoLo\JsonStorage\Query\ConditionProvider;

class ConditionProviderNotNullTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testIsNotNull($expected, $value)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isNotNull($value);

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
                false,
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
                true,
                [],
            ],
            [
                true,
                11,
            ],
            [
                true,
                11.5,
            ],
            [
                true,
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
