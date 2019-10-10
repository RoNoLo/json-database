# FlyDB

A document store which uses any type of filesystem to store documents as JSON.
It uses https://flysystem.thephpleague.com/docs/ to abstract the storage space.

It uses a NoSQL like query system for the documents and aims to use very less 
memory (aka not loading all documents into memory to process them).

There are two usages possible. First as a repository and secondly as a database.