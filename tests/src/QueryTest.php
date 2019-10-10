<?php

namespace RoNoLo\Flydb;

class QueryTest extends TestBase
{
    public function testPredicate()
    {
        $path = __DIR__ . '/fixtures/datastore/querytest';
        $config = new Config($path . '/');
        $repo = new Repository('countries', $config);
        $query = new Query($repo);
        $query->where('cca2', '==', 'GB');

        $predicate = new Predicate();
        $predicate->where('cca2', '==', 'GB');

        $this->assertAttributeEquals($predicate, 'predicate', $query);
    }

    public function testLimit()
    {
        $path = __DIR__ . '/fixtures/datastore/querytest';
        $config = new Config($path . '/');
        $repo = new Repository('countries', $config);
        $query = new Query($repo);
        $query->limit('10');
        $this->assertAttributeEquals([10, 0], 'limit', $query);
        $query->limit(5, 10);
        $this->assertAttributeEquals([5, 10], 'limit', $query);
        $query->limit(9, '11');
        $this->assertAttributeEquals([9, 11], 'limit', $query);

    }

    public function testOrderBy()
    {
        $path = __DIR__ . '/fixtures/datastore/querytest';
        $config = new Config($path . '/');
        $repo = new Repository('countries', $config);
        $query = new Query($repo);
        $query->orderBy('age ASC');
        $this->assertAttributeEquals(['age ASC'], 'orderBy', $query);
        $query->orderBy(['surname DESC', 'age DESC']);
        $this->assertAttributeEquals(['surname DESC', 'age DESC'], 'orderBy', $query);

    }
}
