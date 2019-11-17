<?php

namespace RoNoLo\JsonDatabase;

interface DocumentsGeneratorInterface
{
    /**
     * Returns all documents for further processing (like a query).
     *
     * @param string|null $storeName
     * @return \Generator
     */
    public function generateAllDocuments(string $storeName = null): \Generator;
}