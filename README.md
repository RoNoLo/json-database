# FlyDB

A document store which uses any type of filesystem to store documents as JSON.
It uses https://flysystem.thephpleague.com/docs/ to abstract the storage space.

It uses a NoSQL like query system for the documents and aims to use very less 
memory (aka not loading all documents into memory to process them).

There are two usages possible. First as a repository and secondly as a database.

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
