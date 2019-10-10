<?php

namespace RoNoLo\Flydb;

class ConfigTest extends TestBase
{
    public function testSlashesTidedUp()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'datastore' . DIRECTORY_SEPARATOR . 'writable';
        $config = new Config($path . DIRECTORY_SEPARATOR);

        $this->assertSame($path, $config->getPath());
    }

    public function testSettingOptions()
    {
        $path = __DIR__ . '/fixtures/datastore/writable';
        $config = new Config($path, ['bob' => true]);

        $this->assertTrue($config->getOption('bob'));
        $this->assertNull($config->getOption('nonexistant'));
    }

    public function testSettingFormatter()
    {
        $path = __DIR__ . '/fixtures/datastore/writable';
        $config = new Config($path . '/', [
            'formatter' => new Formatter\PhpFormat,
        ]);

        $this->assertInstanceOf('JamesMoss\\Flywheel\\Formatter\\PhpFormat', $config->getOption('formatter'));
    }

    public function testSettingQueryClass()
    {
        $path = __DIR__ . '/fixtures/datastore/writable';
        $config = new Config($path . '/', [
            'query_class' => '\\stdClass',
        ]);

        $this->assertSame('\\stdClass', $config->getOption('query_class'));
    }

    public function testSettingDocumentClass()
    {
        $path = __DIR__ . '/fixtures/datastore/writable';
        $config = new Config($path . '/', [
            'document_class' => '\\stdClass',
        ]);

        $this->assertSame('\\stdClass', $config->getOption('document_class'));
    }


    public function testSettingAutomaticQueryClass()
    {
        $path = __DIR__ . '/fixtures/datastore/writable';
        $config = new Config($path . '/');

        // This isnt great testing but will do for now.
        $className = '\\JamesMoss\\Flywheel\\';
        $className .= function_exists('apcu_fetch') || function_exists('apc_fetch') ? 'CachedQuery' : 'Query';

        $this->assertSame($className, $config->getOption('query_class'));
    }
}
