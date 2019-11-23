<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RoNoLo\JsonStorage\Store\Query;
use SebastianBergmann\Timer\Timer;

$documents_amount = require_once __DIR__ . DIRECTORY_SEPARATOR . 'setup.php';

$testsRoot = realpath(
    __DIR__ . DIRECTORY_SEPARATOR
);

include_once $testsRoot . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


$fixturesPath = $testsRoot . DIRECTORY_SEPARATOR . 'fixtures';
$datastorePath = $testsRoot . DIRECTORY_SEPARATOR . 'datastore';

$adapter = new Local($datastorePath);

$repoTestPath = 'database_repo';
$indexTestPath = 'database_index';

$flysystem = new Filesystem($adapter);
$flysystem->createDir($repoTestPath);

$datastoreAdapter = new Local($datastorePath . '/' . $repoTestPath);
$indexstoreAdapter = new Local($datastorePath . '/' . $indexTestPath);

$store = new Store($datastoreAdapter);
$db = new Database();
$db->addStore('something', $store);
$db->setIndexStore(new Store($indexstoreAdapter));
$db->addIndex('something', 'age', [
    "age"
]);

// Creating 1 Million
$data = json_decode(gzdecode(file_get_contents($fixturesPath . DIRECTORY_SEPARATOR . 'store_1000_docs.json.gz')));

print "Filling the database. " . $documents_amount . " documents.\n";
print "Index DISABLED on every document write.\n";

$db->setOption('create_indexes', false);

Timer::start();
$j = 0;
while (true) {
    foreach ($data as $document) {
        if (($j + 1) % 1000 == 0) {
            echo ($j + 1) . " Documents\n";
        }

        $document->index = $j;
        $db->put('something', $document);

        $j++;

        if ($j >= $documents_amount) {
            break 2;
        }
    }
}
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

print "Rebuild Index. " . $documents_amount . " documents.\n";

Timer::start();
$db->setOption('create_indexes', true);
$db->rebuildIndexes();
print Timer::secondsToTimeString(Timer::stop()) . "\n";
print "Memory Peak: " . memory_get_peak_usage() . "\n";
print "Memory Allocated: " . memory_get_usage() . "\n";

print "Done.\n";