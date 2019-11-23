<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RoNoLo\JsonStorage\Database\Query;
use SebastianBergmann\Timer\Timer;

$documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

$testsRoot = realpath(
    __DIR__ . DIRECTORY_SEPARATOR
);

include_once $testsRoot . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$datastorePath = $testsRoot . DIRECTORY_SEPARATOR . 'datastore';

$repoTestPath = 'database_repo';
$indexTestPath = 'database_index';

$datastoreAdapter = new Local($datastorePath . '/' . $repoTestPath);
$indexstoreAdapter = new Local($datastorePath . '/' . $indexTestPath);

$store = new Store($datastoreAdapter);
$db = new Database();
$db->addStore('something', $store);
$db->setIndexStore(new Store($indexstoreAdapter));
$db->addIndex('something', 'age', [
    "age"
]);

// Find stuff
$query = new Query($db);

print "Find all documents with age = 20 in " . $documents_amount . " documents.\n";
print "Index usage!\n";

Timer::start();
$result = $query
    ->from('something')
    ->useIndex('age')
    ->find([
        "age" => 20
])->execute();
print "Found: " . $result->count() . "\n";
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

print "\n\n";

print "Change age to = 99 in the found documents.\n";

// Change stuff
Timer::start();
foreach ($result as $id => $data) {
    $data->age = 99;

    $db->put('something', $data);
}
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";
