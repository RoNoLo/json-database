<?php

namespace RoNoLo\Flydb;

use Exception;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Ramsey\Uuid\Uuid;
use RoNoLo\Flydb\Exception\DocumentNotFoundException;
use RoNoLo\Flydb\Exception\DocumentNotStoredException;
use RoNoLo\Flydb\Exception\JsonCollectionImportException;
use RoNoLo\JsonQuery\JsonQuery;

/**
 * Repository
 *
 * Analageous to a table in a traditional RDBMS, a repository is a siloed
 * collection where documents live.
 */
class Repository implements RepositoryInterface
{
    /** @var string */
    protected $name;

    /** @var Filesystem */
    protected $flysystem;

    /** @var int */
    protected $jsonEncodeOptions = 0;

    /** @var Query */
    protected $queryClass;

    /** @var DocumentInterface */
    protected $documentClass;

    /**
     * Constructor
     *
     * @param string $name The name of the repository. Must match /[a-z0-9_]{1,63}+/
     * @param Config $config The config to use for this repo
     * @param AdapterInterface $adapter
     * @throws Exception
     */
    public function __construct(string $name, Config $config, AdapterInterface $adapter)
    {
        $this->flysystem = new Filesystem($adapter);

        // Setup class properties
        $this->name = $name;

        $this->queryClass = $config->getOption('query_class');
        $this->documentClass = $config->getOption('document_class');
        $this->jsonEncodeOptions = $config->getOption('json_encode_options');

        // Ensure the repo name is valid
        $this->validateName($this->name);
    }

    /**
     * Returns the name of this repository
     *
     * @return string The name of the repo
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * A factory method that initialises and returns an instance of a Query object.
     *
     * @return Query A new Query class for this repo.
     */
    public function query()
    {
        $className = $this->queryClass;

        return new $className($this);
    }

    public function import()
    {
        return new ImporterFactory($this);
    }

    public function find(Query $query)
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
     * Returns a single document based on it's ID
     *
     * @param string $id The ID of the document to find
     *
     * @return Document|boolean  The document if it exists, false if not.
     * @throws DocumentNotFoundException
     * @throws \ReflectionException
     */
    public function read($id)
    {
        $path = $this->getPathForDocument($id);

        try {
            $json = $this->flysystem->read($path);
            $data = json_decode($json);

            $document = new $this->documentClass($data);

            $this->setDocumentId($document, $id);

            return $document;
        }
        catch (FileNotFoundException $e) {
            throw new DocumentNotFoundException(sprintf("Document with id `%s` not found.", $id), 0, $e);
        }
    }

    public function remove(DocumentInterface $document)
    {

    }

    public function readMultiple(array $ids): DocumentCollection
    {
        $documents = DocumentCollection::create();

        foreach ($ids as $id) {
            $documents->add($this->read($id));
        }

        return $documents;
    }

    public function storeData($data): Document
    {
        $document = new $this->documentClass($data);

        return $this->storeDocument($document);
    }

    public function storeManyData(array $dataList): DocumentCollection
    {
        $documents = DocumentCollection::create();
        foreach ($dataList as $data) {
            $documents->add($this->storeData($data));
        }

        return $documents;
    }

    public function storeManyDataFromJsonFile($filePath): DocumentCollection
    {
        $dataList = json_decode(file_get_contents($filePath));

        if (!is_array($dataList)) {
            throw new JsonCollectionImportException("The JSON decoded file was not a readable collection");
        }

        if (!count($dataList)) {
            throw new JsonCollectionImportException("The JSON decoded file had an empty collection");
        }

        $documents = DocumentCollection::create();
        foreach ($dataList as $data) {
            $documents->add($this->storeData($data));
        }

        return $documents;
    }

    /**
     * Store a Document in the repository.
     *
     * @param DocumentInterface $document The document to store
     *
     * @return Document if stored, otherwise false
     * @throws \League\Flysystem\FileExistsException
     * @throws \ReflectionException
     * @throws DocumentNotStoredException
     */
    public function storeDocument(DocumentInterface $document): DocumentInterface
    {
        $id = $document->getId();

        // Generate an id if none has been defined
        if (is_null($id)) {
            $id = $this->generateId();
            $this->setDocumentId($document, $id);
        }

        $path = $this->getPathForDocument($id);
        $json = json_encode($document->getPayload(), $this->jsonEncodeOptions);

        if (!$this->flysystem->write($path, $json)) {
            throw new DocumentNotStoredException("The document could not be stored");
        }

        return $document;
    }

    public function storeDocuments(DocumentCollection $documents)
    {
        foreach ($documents as $document) {
            $this->storeDocument($document);
        }

        return $documents;
    }

    /**
     * Delete a document from the repository using its ID.
     *
     * @param mixed $id The ID of the document (or the document itself) to delete
     *
     * @return bool True if deleted, false if not.
     * @throws Exception
     */
    public function delete($id): bool
    {
        if ($id instanceof DocumentInterface) {
            $id = $id->getId();
        }

        $path = $this->getPathForDocument($id);

        return $this->flysystem->delete($path);
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
     * @param string $name The name to validate against
     *
     * @return bool Returns true if valid. Throws an exception if not.
     * @throws Exception
     */
    protected function validateName($name)
    {
        if (!preg_match('/^[a-z][0-9a-z\_]{1,32}$/', $name)) {
            throw new Exception(sprintf('`%s` is not a valid repository name.', $name));
        }

        return true;
    }

    /**
     * Generates a random, unique ID for a document.
     *
     * @return string The generated ID.
     * @throws Exception
     */
    protected function generateId()
    {
        return str_replace('-', '', Uuid::uuid4());
    }

    /**
     * @param $document
     * @param $id
     * @throws \ReflectionException
     */
    protected function setDocumentId($document, $id)
    {
        $reflectClass = new \ReflectionClass($this->documentClass);
        $propertyId = $reflectClass->getProperty('id');
        $propertyId->setAccessible(true);
        $propertyId->setValue($document, $id);
    }
}
