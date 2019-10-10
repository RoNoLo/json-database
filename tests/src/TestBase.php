<?php

namespace RoNoLo\Flydb;

use PHPUnit\Framework\TestCase;

class TestBase extends TestCase
{
    protected $testsRoot;

    protected $fixturesPath;

    protected $datastorePath;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->testsRoot = realpath(
            __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests'
        );

        $this->fixturesPath = $this->testsRoot . DIRECTORY_SEPARATOR . 'fixtures';
        $this->datastorePath = $this->testsRoot . DIRECTORY_SEPARATOR . 'datastore';

        parent::__construct($name, $data, $dataName);
    }

    public function normalizeLineendings($content)
    {
        return str_replace("\r\n", "\n", $content);
    }
}