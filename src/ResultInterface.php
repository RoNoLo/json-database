<?php

namespace RoNoLo\JsonDatabase;

interface ResultInterface
{
    /**
     * How many documents are in the ResultSet.
     *
     * @return int
     */
    public function count(): int;

    /**
     * How many documents were there (before limit or skip was apply'ed)
     *
     * @return int
     */
    public function total(): int;

    /**
     * Returns all documents or a scalar.
     *
     * @param int|null $from
     * @param int|null $to
     * @return mixed
     */
    public function data(?int $from = null, ?int $to = null);
}