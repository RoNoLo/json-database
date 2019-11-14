<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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
        ];
    }
}
