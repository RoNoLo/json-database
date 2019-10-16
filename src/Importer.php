<?php

namespace RoNoLo\Flydb;

use RoNoLo\Flydb\Exception\JsonCollectionImportException;
use RoNoLo\Flydb\Format\FormatInterface;

class Importer
{
    /** @var StoreInterface */
    private $repository;

    /** @var FormatInterface */
    private $format;

    public function __construct(StoreInterface $repository)
    {
        $this->repository = $repository;
    }

    public function setFormat(FormatInterface $format)
    {
        $this->format = $format;
    }

    public function import($data, $isCollection = false): Result
    {
        $data = $this->format->import($data);

        if ($isCollection) {
            if (!is_array($data)) {
                throw new JsonCollectionImportException("The imported data was not an array, but was marked as collection");
            }

            foreach ($data as $item) {
                $this->repository->store($item);
            }
        }
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

    public function storeDocuments(DocumentCollection $documents)
    {
        foreach ($documents as $document) {
            $this->storeDocument($document);
        }

        return $documents;
    }
}