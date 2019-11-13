<?php

namespace RoNoLo\JsonDatabase;

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
                case is_float($comparable):
                case is_string($comparable):
                default:
                    return $value === $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    if (is_object($value) && $value instanceof \DateTime) {
                        $valueDateTime = $value;
                    } elseif (is_string($value)) {
                        $valueDateTime = date_create($value);
                    } else {
                        $unixtime = strtotime($value);
                        if ($unixtime === false) {
                            $valueDateTime = null;
                        } else {
                            $valueDateTime = (new \DateTime())->setTimestamp($unixtime);
                        }
                    }

                    if (!$valueDateTime) {
                        trigger_error(sprintf(
                            "It was not possible to convert value `%s` into a \DateTime for the condition.",
                            $value
                        ), E_USER_NOTICE);
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
                case is_float($comparable):
                case is_string($comparable):
                default:
                    return $value !== $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    if (is_object($value) && $value instanceof \DateTime) {
                        $valueDateTime = $value;
                    } elseif (is_string($value)) {
                        $valueDateTime = date_create($value);
                    } else {
                        $unixtime = strtotime($value);
                        if ($unixtime === false) {
                            $valueDateTime = null;
                        } else {
                            $valueDateTime = (new \DateTime())->setTimestamp($unixtime);
                        }
                    }

                    if (!$valueDateTime) {
                        trigger_error(sprintf(
                            "It was not possible to convert value `%s` into a \DateTime for the condition.",
                            $value
                        ), E_USER_NOTICE);
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
                case is_float($comparable):
                    return $value > $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = date_create($value);
                    if (!$valueDateTime) {
                        trigger_error(sprintf(
                            "It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.",
                            $value
                        ), E_USER_NOTICE);
                    }
                    return $valueDateTime > $comparable;

                default:
                    trigger_error(sprintf("Cannot compare via `\$gt` the value `%s`.", $value), E_USER_NOTICE);
                    return false;
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
                case is_float($comparable):
                    return $value < $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = date_create($value);
                    if (!$valueDateTime) {
                        trigger_error(sprintf(
                            "It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.",
                            $value
                        ), E_USER_NOTICE);
                    }
                    return $valueDateTime < $comparable;

                default:
                    trigger_error(sprintf("Cannot compare via `\$lt` the value `%s`.", $value), E_USER_NOTICE);
                    return false;
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
                case is_float($comparable):
                    return $value >= $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = date_create($value);
                    if (!$valueDateTime) {
                        trigger_error(sprintf(
                            "It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.",
                            $value
                        ), E_USER_NOTICE);
                    }
                    return $valueDateTime >= $comparable;

                default:
                    trigger_error(sprintf("Cannot compare via `\$gte` the value `%s`.", $value), E_USER_NOTICE);
                    return false;
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
                case is_float($comparable):
                    return $value <= $comparable;

                case is_object($comparable) && $comparable instanceof \DateTime:
                    $valueDateTime = date_create($value);
                    if (!$valueDateTime) {
                        trigger_error(sprintf(
                            "It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.",
                            $value
                        ), E_USER_NOTICE);
                    }
                    return $valueDateTime <= $comparable;

                default:
                    trigger_error(sprintf("Cannot compare via `\$lte` the value `%s`.", $value), E_USER_NOTICE);
                    return false;
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
            $value = (string) $value;

            return trim($value) !== '';
        };
    }

    /**
     * Is empty string.
     *
     * @param mixed $value
     * @return \Closure
     */
    public function isEmpty($value)
    {
        return function () use ($value)
        {
            $value = (string) $value;

            return trim($value) === '';
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
}