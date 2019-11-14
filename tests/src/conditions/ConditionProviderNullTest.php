<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class ConditionProviderNullTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testLessThen($expected, $value)
    {
        $conditionExecutor = new ConditionProvider();

        $condition = $conditionExecutor->isNull($value);

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
                true,
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
                false,
                [],
            ],
            [
                false,
                11,
            ],
            [
                false,
                11.5,
            ],
            [
                false,
                "\0",
            ],
            [
                false,
                new \stdClass(),
            ],
        ];
    }
}
