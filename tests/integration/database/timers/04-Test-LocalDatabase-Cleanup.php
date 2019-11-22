<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use RoNoLo\JsonStorage\Store\Query;
use SebastianBergmann\Timer\Timer;

$testsRoot = realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' .
    DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests'
);

$datastorePath = $testsRoot . DIRECTORY_SEPARATOR . 'datastore';

$repoTestPath = 'repo';

$adapter = new Local($datastorePath);
$flysystem = new Filesystem($adapter);
$flysystem->deleteDir($repoTestPath);


