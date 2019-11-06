<?php

namespace RoNoLo\JsonDatabase;

use League\Flysystem\AdapterInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use RoNoLo\JsonDatabase\Exception\DocumentNotFoundException;
use RoNoLo\JsonDatabase\Exception\DocumentNotStoredException;
use RoNoLo\JsonDatabase\Exception\QueryExecutionException;
use RoNoLo\JsonQuery\JsonQuery;

/**
 * Store
 *
 * Analageous to a table in a traditional RDBMS, a repository is a siloed
 * collection where documents live.
 */
class Store implements StoreInterface
{
    /** @var Filesystem */
    protected $flysystem;

    /**
     * Constructor
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->flysystem = new Filesystem($adapter);
    }

    /** @inheritDoc */
    public function putMany(array $documents): array
    {
        // This will force an array as root
        $documents = json_decode(json_encode($documents));

        if (!is_array($documents)) {
            throw new DocumentNotStoredException("Your data was not an array of objects. (To store objects use ->put() instead.)");
        }

        $ids = [];
        foreach ($documents as $document) {
            $ids[] = $this->put($document);
        }

        return $ids;
    }

    /** @inheritDoc */
    public function put($document): string
    {
        // This will force an stdClass object as root
        $document = json_decode(json_encode($document));

        if (!is_object($document)) {
            throw new DocumentNotStoredException("Your data was not an single object. (Maybe an array, you may use ->putMany() instead.)");
        }

        if (!isset($document->__id)) {
            $id = $this->generateId();
            $document->__id = $id;
        } else {
            $id = $document->__id;
        }

        $path = $this->getPathForDocument($id);
        $json = json_encode($document, defined('STORE_JSON_OPTIONS') ? intval(STORE_JSON_OPTIONS) : 0);

        if (!$this->flysystem->put($path, $json)) {
            throw new DocumentNotStoredException(
                sprintf(
                    "The document could not be stored. Writing to flysystem-adapter `%s` failed.",
                    get_class($this->flysystem->getAdapter())
                )
            );
        }

        return $id;
    }

    /** @inheritDoc */
    public function read($id, $assoc = false)
    {
        $path = $this->getPathForDocument($id);

        try {
            $json = $this->flysystem->read($path);
            $document = json_decode($json, $assoc);
            if ($assoc) {
                $document['__id'] = $id;
            } else {
                $document->__id = $id;
            }

            return $document;
        }
        catch (FileNotFoundException $e) {
            throw new DocumentNotFoundException(sprintf("Document with id `%s` not found.", $id), 0, $e);
        }
    }

    /** @inheritDoc */
    public function readMany(array $ids, $assoc = false, $check = true)
    {
        if (!$check) {
            return new DocumentIterator($this, $ids, [], $assoc);
        }

        $exists = [];
        foreach ($ids as $id) {
            $path = $this->getPathForDocument($id);

            if ($this->flysystem->has($path)) {
                $exists[] = $id;
            }
        }

        return new DocumentIterator($this, $exists, [], $assoc);
    }

    /** @inheritDoc */
    public function remove(string $id)
    {
        try {
            return $this->flysystem->delete($this->getPathForDocument($id));
        } catch (FileNotFoundException $e) {
            return true; // Fail silently, because the document is not there anyway.
        }
    }

    /** @inheritDoc */
    public function removeMany(array $ids)
    {
        foreach ($ids as $id) {
            $this->remove($id);
        }

        return true;
    }

    /** @inheritDoc */
    public function find(Query $query, $assoc = false): ResultInterface
    {
        $files = $this->flysystem->listContents('', true);

        $ids = [];
        foreach ($files as $file) {
            if ($file['type'] != 'file') {
                continue;
            }
            if ($file['extension'] != 'json') {
                continue;
            }

            $json = $this->flysystem->read($file['path']);

            // Done here to reuse it for sorting
            $jsonQuery = JsonQuery::fromJson($json);

            if ($query->match($jsonQuery)) {
                if (!$query->sort()) {
                    $ids[$file['filename']] = 1;
                } else {
                    $sortField = key($query->sort());
                    $sortValue = $jsonQuery->get($sortField);

                    if (is_array($sortValue)) {
                        throw new QueryExecutionException("The field to sort by returned more than one value from a document.");
                    }

                    $ids[$file['filename']] = $sortValue;
                }
            }
        }

        // Check for sorting
        if ($query->sort()) {
            $sortDirection = strtolower(current($query->sort()));

            $sortDirection == "asc" ? asort($ids) : arsort($ids);

            // Remove the sort by value
            $ids = array_keys($ids);
        }

        $total = count($ids);

        // Check for 'skip'
        if ($query->skip() > 0) {
            if ($query->skip() > $total) {
                return new ListResult($this);
            } else {
                $ids = array_slice($ids, $query->skip());
            }
        }

        // Check for 'limit'
        if ($query->limit() < count($ids)) {
            $ids = array_slice($ids, 0, $query->limit());
        }

        return new ListResult($this, $query, $ids, $total, $assoc);
    }

    /**
     * Get the filesystem path for a document based on it's ID.
     *
     * @param string $id The ID of the document.
     *
     * @return string The full filesystem path of the document.
     */
    protected function getPathForDocument(string $id): string
    {
        return substr($id, 0, 1) . '/' . substr($id, 0, 2) . '/' . $id . '.json';
    }

    /**
     * Generates a random, unique ID for a document.
     *
     * @return string The generated ID.
     * @throws DocumentNotStoredException
     */
    protected function generateId()
    {
        $breaker = 10;
        while ($breaker) {
            $id = strrev(str_replace('.', '', uniqid('', true)));
            $path = $this->getPathForDocument($id);

            if (!$this->flysystem->has($path)) {
                return $id;
            }

            $breaker--;
        }

        throw new DocumentNotStoredException("It was not possible to generate a unique ID for the document (tried 10 times).");
    }
}
