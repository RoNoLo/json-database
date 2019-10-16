<?php

namespace RoNoLo\Flydb;

use Exception;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Ramsey\Uuid\Uuid;
use RoNoLo\Flydb\Exception\DocumentNotFoundException;
use RoNoLo\Flydb\Exception\DocumentNotStoredException;

/**
 * Repository
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
    public function store($data, string $id = null): string
    {
        // Generate an id if none has been defined
        try {
            if (is_null($id)) {
                $id = $this->generateId();
            }

            $path = $this->getPathForDocument($id);
            $json = json_encode($data, defined('STORE_JSON_OPTIONS') ? intval(STORE_JSON_OPTIONS) : 0);

            if (!$this->flysystem->write($path, $json)) {
                throw new DocumentNotStoredException("The document could not be stored. Writing to drive failed.");
            }

            return $id;
        } catch (FileExistsException $e) {
            throw new DocumentNotStoredException("The document could not be stored.");
        } catch (\ReflectionException $e) {
            throw new DocumentNotStoredException("It was not possible to set the document ID.");
        }
    }

    /** @inheritDoc */
    public function read($id, $assoc = false)
    {
        $path = $this->getPathForDocument($id);

        try {
            $json = $this->flysystem->read($path);
            $data = json_decode($json, $assoc);

            return $data;
        }
        catch (FileNotFoundException $e) {
            throw new DocumentNotFoundException(sprintf("Document with id `%s` not found.", $id), 0, $e);
        }
    }

    /** @inheritDoc */
    public function remove(string $id)
    {
        try {
            return $this->flysystem->delete($this->getPathForDocument($id));
        } catch (FileNotFoundException $e) {
            ; // Fail silently, because the document is not there anyway.
        }
    }

    /** @inheritDoc */
    public function find(Query $query): Result
    {
        $files = $this->flysystem->listContents('', true);

        $ids = [];
        foreach ($files as $file) {
            if ($file['type'] != 'file') {
                continue;
            }

            $json = $this->flysystem->read($file['path']);

            if ($query->match($json)) {
                $ids[] = $file['filename'];
            }
        }

        return new Result($this, $ids, count($ids));
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
     */
    protected function generateId()
    {
        return uniqid('', true);
    }
}
