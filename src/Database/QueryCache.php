<?php

namespace RoNoLo\JsonStorage\Database;

use RoNoLo\JsonStorage\Database;
use RoNoLo\JsonStorage\Exception\DatabaseRuntimeException;
use RoNoLo\JsonStorage\Exception\DocumentNotStoredException;
use RoNoLo\JsonStorage\Exception\QueryExecutionException;

/**
 * The QueryCache encapsulates the usage of it.
 *
 * The QueryCache is very simple by design. Your $query->find(...) has to
 * match exactly to have an effect.
 *
 * The QueryCache creates, finds and removes caches of IDs of query requests
 * which had at least one document as result.
 *
 * As per default the cache lasts for 1 hour or until a put or remove to the
 * same store was executed. @todo: add the ttl option to be set from the DatabaseConfig.
 *
 * @package RoNoLo\JsonStorage\Database
 */
class QueryCache
{
    /** @var string The internal name of the flysystem directory */
    const STORE_NAME = '__query_cache';

    /** @var string The TTL option for later use */
    const OPTION_TTL_IN_SECONDS = 'query_cache_ttl';

    /** @var Database */
    protected $database;

    /** @var int */
    protected $ttl = 3600;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Finds a cached IDs result set, if the storeName and the query filters match.
     *
     * @param string $storeName The store which holds the requested data
     * @param array $queryFilters The filter query
     *
     * @return array|false|null false means the QueryCache JSON store was requested, null mean a miss, array is a hit
     * @throws QueryExecutionException
     */
    public function find(string $storeName, array $queryFilters)
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
                        '$eq' => json_encode($queryFilters),
                    ],
                    "datetime" => [
                        '$gte' => new \DateTime("now")
                    ]
                ]
            ])
            ->sort("datetime", "desc")
            ->limit(1)
            ->cache(false)
            ->execute();

        if ($result->total() == 1) {
            return (array) $result[0]->ids;
        }

        return null;
    }

    /**
     * Writes to the QueryCache.
     *
     * @param string $storeName Name of the JSON store
     * @param array $queryFilters The find() filters
     * @param array $ids The IDs which shall be saved
     *
     * @throws DatabaseRuntimeException
     * @throws DocumentNotStoredException
     */
    public function put(string $storeName, array $queryFilters, array $ids)
    {
        if ($storeName == self::STORE_NAME) {
            return;
        }

        // @TODO: The cleanup here could be an performance issue.
        $this->remove($storeName, $queryFilters);

        // We set the datetime into the future, thus every entry older than now is invalid on request.
        $this->database->put(self::STORE_NAME, [
            'storeName' => $storeName,
            'query' => json_encode($queryFilters),
            'ids' => $ids,
            'datetime' => (new \DateTime("+" . $this->ttl . " seconds"))->format(\DateTime::ISO8601),
        ]);
    }

    /**
     * Removes entries from the QueryCache JSON store.
     *
     * There are 4 ways:
     *   - by JSON store
     *   - by JSON store and query filter
     *   - by JSON store, query filter and datetime
     *   - by JSON store and datetime
     *
     * @param string $storeName
     * @param array|null $queryFilters
     * @param null $datetime
     *
     * @throws DatabaseRuntimeException
     * @throws QueryExecutionException
     */
    public function remove(string $storeName, array $queryFilters = null, $datetime = null)
    {
        $query = new Database\Query($this->database);

        $conditions = [
            '$and' => [
                "storeName" => [
                    '$eq' => $storeName,
                ],
            ]
        ];

        if (!is_null($queryFilters)) {
            $conditions['$and']["query"] = [
                '$eq' => json_encode($queryFilters),
            ];
        }

        if (!is_null($datetime)) {
            $conditions['$and']["datetime"] = [
                '$lte' => $datetime
            ];
        }

        $result = $query
            ->from(self::STORE_NAME)
            ->fields(["ids"])
            ->find($conditions)
            ->cache(false)
            ->execute();

        $this->database->removeMany(self::STORE_NAME, $result);
    }

    /**
     * Removes the whole QueryCache entries.
     *
     * @throws DatabaseRuntimeException
     */
    public function truncateEverything()
    {
        $this->database->truncate(self::STORE_NAME);
    }

    /**
     * Removes only QueryCache entries for a specific store.
     *
     * @param string $storeName
     *
     * @throws DatabaseRuntimeException
     * @throws QueryExecutionException
     */
    public function truncate(string $storeName)
    {
        $this->remove($storeName);
    }
}