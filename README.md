# JsonStorage

*Warning: Heavy development! Do not use below 1.0.0!*

A document store which uses any type of filesystem to store documents as JSON.
It uses https://flysystem.thephpleague.com/ to abstract the storage space.

It uses a NoSQL like query system for the documents and aims to use very less 
memory (aka not loading all documents into memory to process them).

## Json Store Usage

First specify the adapter which shall be used to actually store the JSON files to
a disc/cloud/memory/zip. See https://github.com/thephpleague/flysystem 
to find the one which fits your needs. You have to init the adapter with the 
correct parameters. 

```php
// First create the adapter
$adapter = new Local('some/path/persons');
// Secondly create the JsonDB
$store = new Store($adapter);
```

The store is now ready. We can now store, read, delete and update documents. As a very
basic usage, we can read every document back by ID.

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

It is also possible to query documents in a CouchDB like fashion from the store.

```php
$query = new Store\Query($store);
$result = $query->find([
    "name" => "Bernd"
]);

// An iterator can be used to fetch one by one all documents

foreach ($result as $id => $document) {
    ; // do something with the document
}
```

## Json Database Usage

When using the JsonStorage as database, the usage will be a little more parameter
heavy, but it will come with a few benefits. The major difference is referenced documents.

A Referenced document is one which lives in it own store, but is maybe shared by other
store documents. Here an example how it looks under the hood.

```php
$db = new Database();
$db->addStore('person', new Store(new MemoryAdapter());
$db->addStore('interests', new Store(new MemoryAdapter());

// Create a few interests
$hobby1 = $db->put('interests', ['name' => 'Music', 'stars' => 4], true);
$hobby2 = $db->put('interests', ['name' => 'Boxen', 'stars' => 3.4], true);
$hobby3 = $db->put('interests', ['name' => 'Movies', 'stars' => 5], true);

// Create some persons
$person1 = $db->put('person', [
    'name' => 'Ronald',   
    'interests' => [$hobby1, $hobby2]
]);
$person2 = $db->put('person', [
    'name' => 'Ellen',   
    'interests' => [$hobby2, $hobby3]
]);
$person3 = $db->put('person', [
    'name' => 'James',   
    'interests' => []
]);
$person4 = $db->put('person', [
    'name' => 'Katja',   
    'interests' => [$hobby3, $hobby1, $hobby2]
]);
``` 

## Goals

- No real database needed like SqlLite, Mysql, MongoDB, CouchDB ...)
- PHP 7.2+
- Document Store aka NoSQL
- JSON as format of storage
- (very) low memory usage even for huge results
- NoSQL like query syntax (CouchDB style)
- Repository and database (document relations) usage options
- Abstract data location via https://flysystem.thephpleague.com/
