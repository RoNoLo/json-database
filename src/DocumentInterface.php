<?php

namespace RoNoLo\Flydb;

/**
 * Interface for documents
 */
interface DocumentInterface
{
    /**
     * Constructor
     *
     * @param array $data array or object
     */
    public function __construct($data);

    /**
     * Get the document ID.
     *
     * @return null|string
     */
    public function getId(): ?string;

    public function getPayload();

    public function asArray(): array;
}
