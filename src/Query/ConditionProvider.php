<?php

namespace RoNoLo\JsonStorage\Query;

class ConditionProvider
{
    const RULES = [
        '$eq' => 'isEqual',
        '$neq' => 'isNotEqual',
        '$gt' => 'isGreaterThan',
        '$gte' => 'isGreaterThanOrEqual',
        '$lt' => 'isLessThan',
        '$lte' => 'isLessThanOrEqual',
        '$in'    => 'isIn',
        '$nin' => 'isNotIn',
        '$null' => 'isNull',
        '$n' => 'isNull',
        '$notnull' => 'isNotNull',
        '$nn' => 'isNotNull',
        '$contains' => 'contains',
        '$c' => 'contains',
        '$ne' => 'isNotEmpty',
        '$e' => 'isEmpty',
        '$regex' => 'isRegExMatch',
    ];

    public function get($op, $value, $comparable)
    {
        switch ($op) {
            case '$eq': return $this->isEqual($value, $comparable);
            case '$neq': return $this->isNotEqual($value, $comparable);
            case '$gt': return $this->isGreaterThan($value, $comparable);
            case '$gte': return $this->isGreaterThanOrEqual($value, $comparable);
            case '$lt': return $this->isLessThan($value, $comparable);
            case '$lte': return $this->isLessThanOrEqual($value, $comparable);
            case '$in': return $this->isIn($value, $comparable);
            case '$nin': return $this->isNotIn($value, $comparable);
            case '$n':
            case '$null': return $this->isNull($value);
            case '$nn':
            case '$notnull': return $this->isNotNull($value);
            case '$c':
            case '$contains': return $this->contains($value, $comparable);
            case '$ne': return $this->isNotEmpty($value);
            case '$e': return $this->isEmpty($value);
            case '$regex': return $this->isRegExMatch($value, $comparable);
            default:
                throw new \Exception(sprintf("%s is not implemented yet", $op));
        }
    }


    /**
     * Simple equals
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return \Closure
     */
    public function isEqual($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            switch (true) {
                // We are strict with scalars
                case is_int($comparable):
                    return intval($value) === $comparable;

                case is_float($comparable):
                    return floatval($value) === $comparable;

                case is_string($comparable):
                    return (string) $value === $comparable;

                default:
                    return $value === $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = $this->toDateTime($value);

                    if (!$valueDateTime) {
                        return false;
                    }

                    return $valueDateTime == $comparable;
            }
        };
    }

    /**
     * Simple not equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return \Closure
     */
    public function isNotEqual($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            switch (true) {
                // We are strict with scalars
                case is_int($comparable):
                    return intval($value) !== $comparable;

                case is_float($comparable):
                    return floatval($value) !== $comparable;

                case is_string($comparable):
                    return (string) $value !== $comparable;

                default:
                    return $value !== $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = $this->toDateTime($value);

                    if (!$valueDateTime) {
                        return false;
                    }

                    return $valueDateTime != $comparable;

            }
        };
    }

    /**
     * Strict greater than
     *
     * @param mixed $value
     * @param mixed $comparable
     * @return \Closure
     */
    public function isGreaterThan($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            switch (true) {
                case is_int($comparable):
                    return intval($value) > $comparable;

                case is_float($comparable):
                    return floatval($value) > $comparable;

                case is_string($comparable):
                    return (string) $value > $comparable;

                default:
                    return $value > $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = $this->toDateTime($value);

                    if (!$valueDateTime) {
                        return false;
                    }

                    return $valueDateTime > $comparable;
            }
        };
    }

    /**
     * Strict less than
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return \Closure
     */
    public function isLessThan($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            switch (true) {
                case is_int($comparable):
                    return intval($value) < $comparable;

                case is_float($comparable):
                    return floatval($value) < $comparable;

                case is_string($comparable):
                    return (string) $value < $comparable;

                default:
                    return $value < $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = $this->toDateTime($value);

                    if (!$valueDateTime) {
                        return false;
                    }

                    return $valueDateTime < $comparable;
            }
        };
    }

    /**
     * Greater or equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return \Closure
     */
    public function isGreaterThanOrEqual($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            switch (true) {
                case is_int($comparable):
                    return intval($value) >= $comparable;

                case is_float($comparable):
                    return floatval($value) >= $comparable;

                case is_string($comparable):
                    return (string) $value >= $comparable;

                default:
                    return $value >= $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = $this->toDateTime($value);

                    if (!$valueDateTime) {
                        return false;
                    }

                    return $valueDateTime >= $comparable;
            }
        };
    }

    /**
     * Less or equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return \Closure
     */
    public function isLessThanOrEqual($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            switch (true) {
                case is_int($comparable):
                    return intval($value) <= $comparable;

                case is_float($comparable):
                    return floatval($value) <= $comparable;

                case is_string($comparable):
                    return (string) $value <= $comparable;

                default:
                    return $value <= $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = $this->toDateTime($value);

                    if (!$valueDateTime) {
                        return false;
                    }

                    return $valueDateTime <= $comparable;
            }
        };
    }

    /**
     * In array
     *
     * @param mixed $value
     * @param array $comparable
     *
     * @return \Closure
     */
    public function isIn($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            return (is_array($comparable) && in_array($value, $comparable));
        };
    }

    /**
     * Not in array
     *
     * @param mixed $value
     * @param array $comparable
     *
     * @return \Closure
     */
    public function isNotIn($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            return (is_array($comparable) && !in_array($value, $comparable));
        };
    }

    /**
     * Is null equal
     *
     * @param mixed $value
     *
     * @return \Closure
     */
    public function isNull($value)
    {
        return function () use ($value)
        {
            return is_null($value);
        };
    }

    /**
     * Is not null equal
     *
     * @param mixed $value
     *
     * @return \Closure
     */
    public function isNotNull($value)
    {
        return function () use ($value)
        {
            return !is_null($value);
        };
    }

    /**
     * Is not empty string.
     *
     * @param mixed $value
     *
     * @return \Closure
     */
    public function isNotEmpty($value)
    {
        return function () use ($value)
        {
            if (is_array($value)) {
                return count($value) !== 0;
            } elseif (is_string($value)) {
                return trim($value) !== '';
            }

            return true;
        };
    }

    /**
     * Is empty string or empty array.
     *
     * @param mixed $value
     * @return \Closure
     */
    public function isEmpty($value)
    {
        return function () use ($value)
        {
            if (is_array($value)) {
                return count($value) === 0;
            } elseif (is_string($value)) {
                return trim($value) === '';
            }

            return false;
        };
    }

    /**
     * Match with pattern
     *
     * @param mixed $value
     * @param string $comparable
     *
     * @return \Closure
     */
    public function isRegExMatch($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            if (!is_string($comparable)) {
                return false;
            }

            $comparable = trim($comparable);
            if (preg_match($comparable, $value)) {
                return true;
            }

            return false;
        };
    }

    /**
     * Contains substring in string
     *
     * @param string $value
     * @param string $comparable
     *
     * @return \Closure
     */
    public function contains($value, $comparable)
    {
        return function () use ($value, $comparable)
        {
            return (strpos($value, $comparable) !== false);
        };
    }

    /**
     * @param mixed $value
     * @return \DateTime|null
     * @throws \Exception
     */
    private function toDateTime($value)
    {
        if (is_object($value) && $value instanceof \DateTime) {
            return $value;
        } elseif (is_string($value)) {
            $dateTime = date_create($value);

            return $dateTime ? $dateTime : null;
        } else {
            $unixtime = strtotime($value);
            if ($unixtime === false) {
                return null;
            } else {
                return (new \DateTime())->setTimestamp($unixtime);
            }
        }
    }
}