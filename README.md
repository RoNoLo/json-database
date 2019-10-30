# JsonDatabase

A document store which uses any type of filesystem to store documents as JSON.
It uses https://flysystem.thephpleague.com/ to abstract the storage space.

It uses a NoSQL like query system for the documents and aims to use very less 
memory (aka not loading all documents into memory to process them).

## Json Store Usage

First specify the adapter which shall be used to actually store the JSON files to
a disc/cloud/zip. See https://github.com/thephpleague/flysystem 
to find the one which fits your needs. You have to init the adapter with the 
correct parameters. 

```php
// First create the adapter
$adapter = new Local('some/path/persons');
// Secondly create the JsonDB
$store = new Store($adapter);
```

The store is now ready. We can now store, read, delete and update documents.

```php
$document = file_get_contents('file/with/json/object.json');
// store a document
$id = $store->put($document);
// read a document
$document = $store->read($id);
// update document
$document->foobar = "Heinz";
$store->put($document);
// remove document
$store->remove($id); 
```

A CouchDB like query can be used to find documents in the store.

```php
$query = new Query($store);
$result = $query->find([
    "name" => "Bernd"
]);

// The $resultset will contain only document IDs, which can be acced by $result->getIds();
// An iterator can be used to fetch one by one all documents

foreach ($result as $id => $document) {
    ; // do something with the document
}

```

## Notice

This repository contains the bare minimum please see https://github.com/ronolo/jsondatabase-tools
for tools to import or export data.

## Goals

- No real database needed like SqlLite, Mysql, MongoDB, CouchDB ...)
- PHP 7.2+
- Document Store aka NoSQL
- JSON as format of storage
- (very) low memory usage even for huge results
- NoSQL like query syntax (CouchDB style)
- Caching via APC
- Repository and database (document relations) usage options
- Exports
- Imports
- Abstract data location via https://flysystem.thephpleague.com/

## Inspiration

Based on the https://github.com/jamesmoss/flywheel project and started via cloning and
adding features I like. But quickly realized that the diff was getting larger and larger.
Therefore I decided to rename it and develop it heavily myself further.
