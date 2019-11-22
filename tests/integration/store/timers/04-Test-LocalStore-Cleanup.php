<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RoNoLo\JsonStorage\Store\Query;
use SebastianBergmann\Timer\Timer;

$testsRoot = realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests'
);

include_once $testsRoot . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$datastorePath = $testsRoot . DIRECTORY_SEPARATOR . 'datastore';

$repoTestPath = 'store_repo';

$datastoreAdapter = new Local($datastorePath . '/' . $repoTestPath);

$store = new Store($datastoreAdapter);

print "Delete all documents\n";

Timer::start();
$store->truncate();
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

$adapter = new Local($datastorePath);
$flysystem = new Filesystem($adapter);
$flysystem->deleteDir($repoTestPath);
