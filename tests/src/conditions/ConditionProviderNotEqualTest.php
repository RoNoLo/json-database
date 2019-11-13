<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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
        ];
    }
}
