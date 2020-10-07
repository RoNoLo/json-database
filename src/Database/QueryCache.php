<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;

class QueryCache
{
    const STORE_NAME = '__query_cache';

    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function find(string $storeName, string $queryString)
    {
        if ($storeName == self::STORE_NAME) {
            return false;
        }

        $query = new Database\Query($this->database);

        $result = $query
            ->from(self::STORE_NAME)
            ->fields(["ids"])
            ->find([
                '$and' => [
                    "storeName" => [
                        '$eq' => $storeName,
                    ],
                    "query" => [
                        '$eq' => $queryString,
                    ],
//                    "datetime" => [
//                        '$lte' => new \DateTime("-1 hour")
//                    ]
                ]
            ])
            ->execute();

        if ($result->total() == 1) {
            return (array) $result[0]->ids;
        } elseif ($result->total() == 0) {
            return [];
        }

        return null;
    }

    public function put($storeName, $queryString, $ids)
    {
        if ($storeName == self::STORE_NAME) {
            return;
        }

        $this->database->put(self::STORE_NAME, [
            'storeName' => $storeName,
            'query' => $queryString,
            'ids' => $ids,
            'datetime' => (new \DateTime())->format(\DateTime::ISO8601),
        ]);
    }
}