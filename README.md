# FlyDB

A document store which uses any type of filesystem to store documents as JSON.
It uses https://flysystem.thephpleague.com/docs/ to abstract the storage space.

It uses a NoSQL like query system for the documents and aims to use very less 
memory (aka not loading all documents into memory to process them).

There are two usages possible. First as a repository and secondly as a database.

## Motivation

## Inspiration

Based on the https://github.com/jamesmoss/flywheel project and started via cloning and
adding features I like. But quickly realized that the diff was getting larger and larger.
Therefore I decided to rename it and develop it heavily myself further.