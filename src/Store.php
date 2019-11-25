<?php

namespace RoNoLo\JsonStorage;

use League\Flysystem\{AdapterInterface, FileNotFoundException, Filesystem};
use RoNoLo\JsonStorage\Exception\{DatabaseRuntimeException, DocumentNotFoundException, DocumentNotStoredException};
use RoNoLo\JsonStorage\Store\Config;
use RoNoLo\JsonStorage\Store\DocumentIterator;

/**
 * Store
 *
 * Analageous to a table in a traditional RDBMS, a store is a collection where documents live.
 */
class Store
{
    const STORE_INDEX_FILE = '__index.json';

    /** @var Filesystem */
    protected $flysystem;

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $index = [];

    public static function create(Config $config)
    {
        return new static($config->getAdapter(), $config->getOptions());
    }

    /**
     * Constructor
     *
     * @param AdapterInterface $adapter
     * @param array $options
     * @throws FileNotFoundException
     */
    protected function __construct(AdapterInterface $adapter, array $options = [])
    {
        $this->options = [] + $options;
        $this->flysystem = new Filesystem($adapter);

        // Init or rebuild the Index.
        if (!$this->flysystem->has(self::STORE_INDEX_FILE)) {
            $this->rebuildIndex();
        }

        $indexJson = $this->flysystem->read(self::STORE_INDEX_FILE);
        $this->index = json_decode($indexJson, true);
    }

    public function __destruct()
    {
        $this->flysystem->put(self::STORE_INDEX_FILE, json_encode($this->index));
    }

    /**
     * Tells if a document exists.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        $path = $this->getPathForDocument($id);

        return $this->flysystem->has($path);
    }

    /**
     * Stores many documents to the store.
     *
     * @param array $documents
     *
     * @return array Of IDs
     * @throws DocumentNotStoredException
     */
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

    /**
     * Stores a document or data structure to the store.
     *
     * It has to be a single document i.e. a \stdClass after converting it via json_encode().
     *
     * @param \stdClass|array $document
     *
     * @return string
     * @throws DocumentNotStoredException
     */
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

        $this->index[] = $id;

        return $id;
    }

    /**
     * Reads a document from the store.
     *
     * @param string $id
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     *
     * @return bool|false|mixed|string
     * @throws DocumentNotFoundException
     */
    public function read(string $id, $assoc = false)
    {
        $path = $this->getPathForDocument($id);

        try {
            $json = $this->flysystem->read($path);

            if (is_null($assoc)) {
                return $json;
            }

            $document = json_decode($json, !!$assoc);

            return $document;
        }
        catch (FileNotFoundException $e) {
            throw new DocumentNotFoundException(sprintf("Document with id `%s` not found.", $id), 0, $e);
        }
    }

    /**
     * Reads documents from the store.
     *
     * @param array $ids
     * @param bool $assoc Will be used for json_decode's 2nd argument.
     * @param bool $check If false, no documents exists check will be executed in advance, just the Iterator will be created.
     *
     * @return DocumentIterator
     */
    public function readMany(array $ids, $assoc = false, $check = true)
    {
        if (!$check) {
            return new DocumentIterator($this, $ids, [], $assoc);
        }

        $existIds = [];
        foreach ($ids as $id) {
            if ($this->has($id)) {
                $existIds[] = $id;
            }
        }

        return new DocumentIterator($this, $existIds, [], $assoc);
    }

    /**
     * Removes a document from the store.
     *
     * @param string $id
     */
    public function remove(string $id)
    {
        try {
            foreach (array_keys($this->index, $id) as $key) {
                unset($this->index[$key]);
            }

            $this->flysystem->delete($this->getPathForDocument($id));
        } catch (FileNotFoundException $e) {
            ;
        }
    }

    /**
     * Removes many documents from the store.
     *
     * @param array $ids
     *
     * @return void
     */
    public function removeMany(array $ids)
    {
        foreach ($ids as $id) {
            $this->remove($id);
        }
    }

    /**
     * Removes all documents from the store.
     *
     * @return void
     */
    public function truncate()
    {
        $contents = $this->flysystem->listContents('');

        foreach ($contents as $content) {
            if ($content['type'] == 'dir') {
                $this->flysystem->deleteDir($content['path']);
            }
        }

        $this->index = [];
    }

    public function count(): int
    {
        return count($this->index);
    }

    /**
     * Returns all documents for further processing.
     *
     * @return \Generator
     * @throws FileNotFoundException
     */
    public function documentsGenerator(): \Generator
    {
        foreach ($this->index as $id) {
            yield $this->flysystem->read($this->getPathForDocument($id));
        }
    }

    public function rebuildIndex()
    {
        $index = [];
        foreach ($this->flysystem->listContents('', true) as $content) {
            if ($content['type'] != 'file') {
                continue;
            }
            if ($content['extension'] != 'json') {
                continue;
            }
            if ($content['basename'] == self::STORE_INDEX_FILE) {
                continue;
            }

            $documentJson = $this->flysystem->read($content['path']);
            $document = json_decode($documentJson);
            $index[] = $document->__id;
        }

        $this->flysystem->put(self::STORE_INDEX_FILE, json_encode($index));
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
