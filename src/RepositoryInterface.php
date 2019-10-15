<?php

namespace RoNoLo\Flydb;

interface RepositoryInterface
{
    public function add(Document $document);

    public function read($id): DocumentInterface;

    public function remove(DocumentInterface $document);

    public function find(Query $query);
}
