<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class ConditionExecutorTest extends TestBase
{
    /**
     * @dataProvider equalProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionExecutor();

        $result = $conditionExecutor->equal($value, $comparable);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider strictEqualProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testStrictEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionExecutor();

        $result = $conditionExecutor->strictEqual($value, $comparable);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider notEqualProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testNotEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionExecutor();

        $result = $conditionExecutor->notEqual($value, $comparable);

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider strictNotEqualProvider
     *
     * @param $expected
     * @param $value
     * @param $comparable
     */
    public function testStrictNotEqual($expected, $value, $comparable)
    {
        $conditionExecutor = new ConditionExecutor();

        $result = $conditionExecutor->strictNotEqual($value, $comparable);

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
        ];
    }

    public function strictEqualProvider()
    {
        return [
            [
                true,
                10,
                10
            ],
            [
                false,
                10,
                10.0
            ],
            [
                false,
                null,
                "\0"
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
                0x0,
                10
            ],
        ];
    }

    public function strictNotEqualProvider()
    {
        return [
            [
                false,
                10,
                10
            ],
            [
                true,
                10,
                10.0
            ],
            [
                true,
                null,
                "\0"
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
                0x0,
                10
            ],
        ];
    }
}
