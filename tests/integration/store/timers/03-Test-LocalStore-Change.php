<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RoNoLo\JsonStorage\Store\Query;
use SebastianBergmann\Timer\Timer;

$documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

$testsRoot = realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests'
);

include_once $testsRoot . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$datastorePath = $testsRoot . DIRECTORY_SEPARATOR . 'datastore';

$repoTestPath = 'store_repo';

$datastoreAdapter = new Local($datastorePath . '/' . $repoTestPath);

$store = new Store($datastoreAdapter);

// Find stuff
$query = new Query($store);

print "Find all documents with age = 20 in " . $documents_amount . " documents. (Should be 0)\n";

Timer::start();
$result = $query->find([
    "age" => 20
])->execute();
print "Found: " . $result->count() . "\n";
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

print "\n\n";

print "Find all documents with age = 99 in " . $documents_amount . " documents.\n";

Timer::start();
$result = $query->find([
    "age" => 99
])->execute();
print "Found: " . $result->count() . "\n";
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

print "Delete all documents with age = 99.\n";

Timer::start();
$store->removeMany($result->getIds());
print Timer::secondsToTimeString(Timer::stop()) . "\n";

print "Find all documents with age = 99 in " . $documents_amount . " documents. (Should be 0)\n";

Timer::start();
$result = $query->find([
    "age" => 99
])->execute();
print "Found: " . $result->count() . "\n";
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

print "Get the number of documents in the store. (Should be 98.400).\n";

Timer::start();
print "Documents in store: " . $store->count() . "\n";
print Timer::secondsToTimeString(Timer::stop()) . "\n";



