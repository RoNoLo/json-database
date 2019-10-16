<?php

namespace RoNoLo\Flydb;

interface RepositoryInterface
{
    public function store($document): DocumentInterface;

    public function read($id): DocumentInterface;

    public function remove($document);

    public function find(Query $query);
}
