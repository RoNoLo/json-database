# Json Database

*Warning: Heavy development! Do not use below 1.0.0!*

A document store which uses any type of filesystem to store documents as JSON.
It uses https://flysystem.thephpleague.com/ to abstract the storage space.

It uses a NoSQL like query system for the documents and aims to use very less 
memory (aka not loading all documents into memory to process them).

This is an _extension_ of the https://github.com/RoNoLo/json-store to have relations
between JSON documents.

## Usage

When using the JSON storage as database, the usage will be a little more parameter
heavy, but it will come with a few benefits. The major difference is referenced documents.

There are two flavors of databases provided. The simple one adds just referenced documents
support and the second one adds also index support for faster queries, if you still want 
to use this instead of a full blown NoSQL database. 

Simple:

```php
$config = Database\Config();
$config->addStore('person', (new Store\Config())->setAdapter(new MemoryAdapter()));
$config->addStore('interests', (new Store\Config())->setAdapter(new MemoryAdapter()));

$database = Database::create($config);
```

This will tell the database, that it knows 2 JSON stores under the hood and when ever
a query against the database will happen the database will look for referenced documents
put them in place and return the documents normally. 

Indexed:

```php
$config = Database\Config();
$config->addStore('person', (new Store\Config())->setAdapter(new Local('/foo/bar/persons')));
$config->addStore('interests', (new Store\Config())->setAdapter(new Local('/foo/bar/interests')));
$config->setIndexStore((new Store\Config())->setAdapter(new MemoryAdapter()));
$config->addIndex('person', 'age', ['age', 'created']);

$database = Database::create($config);
```

This database does the same as above, but has an extra store which only stores indicies of
certain keys. whenever a query is fired it will fistly check if an index can be used to find
documents, and if so everything is faster. 

A Referenced document is one which lives in it own store, but is maybe shared by other
store documents. Here an example how it looks under the hood.

```php
$config = Database\Config();

$config->addStore('person', (new Store\Config())->setAdapter(new Local('/foo/bar/persons')));
$config->addStore('interests', (new Store\Config())->setAdapter(new Local('/foo/bar/interests')));
$database = Database::create($config);

// Create a few interests
$hobby1 = $database->put('interests', ['name' => 'Music', 'stars' => 4], true);
$hobby2 = $database->put('interests', ['name' => 'Boxen', 'stars' => 3.4], true);
$hobby3 = $database->put('interests', ['name' => 'Movies', 'stars' => 5], true);

// Create some persons
$person1 = $database->put('person', [
    'name' => 'Ronald',   
    'interests' => [$hobby1, $hobby2]
]);
$person2 = $database->put('person', [
    'name' => 'Ellen',   
    'interests' => [$hobby2, $hobby3]
]);
$person3 = $database->put('person', [
    'name' => 'James',   
    'interests' => []
]);
$person4 = $database->put('person', [
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
