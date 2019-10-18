<?php

namespace RoNoLo\Flydb;

class ConditionExecutor
{
    /**
     * Simple equals
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function equal($value, $comparable)
    {
        return $value == $comparable;
    }

    /**
     * Strict equals
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function strictEqual($value, $comparable)
    {
        return $value === $comparable;
    }

    /**
     * Simple not equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function notEqual($value, $comparable)
    {
        return $value != $comparable;
    }

    /**
     * Strict not equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function strictNotEqual($value, $comparable)
    {
        return $value !== $comparable;
    }

    /**
     * Strict greater than
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function greaterThan($value, $comparable)
    {
        switch (true) {
            case is_int($comparable):
            case is_float($comparable):
                return $value > $comparable;
            case is_object($comparable) && $comparable instanceof \DateTime:
                $valueDateTime = date_create($value);
                if (!$valueDateTime) {
                    trigger_error(sprintf("It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.", $value), E_USER_NOTICE);
                }
                return $valueDateTime > $comparable;
            default:
                trigger_error(sprintf("Cannot compare via `\$gt` the value `%s`.", $value), E_USER_NOTICE);
                return false;
        }
    }

    /**
     * Strict less than
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function lessThan($value, $comparable)
    {
        switch (true) {
            case is_int($comparable):
            case is_float($comparable):
                return $value < $comparable;
            case is_object($comparable) && $comparable instanceof \DateTime:
                $valueDateTime = date_create($value);
                if (!$valueDateTime) {
                    trigger_error(sprintf("It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.", $value), E_USER_NOTICE);
                }
                return $valueDateTime < $comparable;
            default:
                trigger_error(sprintf("Cannot compare via `\$gt` the value `%s`.", $value), E_USER_NOTICE);
                return false;
        }
    }

    /**
     * Greater or equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function greaterThanOrEqual($value, $comparable)
    {
        switch (true) {
            case is_int($comparable):
            case is_float($comparable):
                return $value >= $comparable;
            case is_object($comparable) && $comparable instanceof \DateTime:
                $valueDateTime = date_create($value);
                if (!$valueDateTime) {
                    trigger_error(sprintf("It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.", $value), E_USER_NOTICE);
                }
                return $valueDateTime >= $comparable;
            default:
                trigger_error(sprintf("Cannot compare via `\$gt` the value `%s`.", $value), E_USER_NOTICE);
                return false;
        }
    }

    /**
     * Less or equal
     *
     * @param mixed $value
     * @param mixed $comparable
     *
     * @return bool
     */
    public function lessThanOrEqual($value, $comparable)
    {
        switch (true) {
            case is_int($comparable):
            case is_float($comparable):
                return $value <= $comparable;
            case is_object($comparable) && $comparable instanceof \DateTime:
                $valueDateTime = date_create($value);
                if (!$valueDateTime) {
                    trigger_error(sprintf("It was not possible to convert value `%s` into a \DateTime with ATOM format (!) for the condition.", $value), E_USER_NOTICE);
                }
                return $valueDateTime <= $comparable;
            default:
                trigger_error(sprintf("Cannot compare via `\$gt` the value `%s`.", $value), E_USER_NOTICE);
                return false;
        }
    }

    /**
     * In array
     *
     * @param mixed $value
     * @param array $comparable
     *
     * @return bool
     */
    public function in($value, $comparable)
    {
        return (is_array($comparable) && in_array($value, $comparable));
    }

    /**
     * Not in array
     *
     * @param mixed $value
     * @param array $comparable
     *
     * @return bool
     */
    public function notIn($value, $comparable)
    {
        return (is_array($comparable) && !in_array($value, $comparable));
    }

    /**
     * Is null equal
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isNull($value, $comparable)
    {
        return is_null($value);
    }

    /**
     * Is not null equal
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isNotNull($value, $comparable)
    {
        return !is_null($value);
    }

    /**
     * Is not null equal
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isNotEmpty($value, $comparable)
    {
        $value = (string) $value;

        return trim($value) !== '';
    }

    /**
     * Start With
     *
     * @param mixed $value
     * @param string $comparable
     *
     * @return bool
     */
    public function startWith($value, $comparable)
    {
        if (is_array($comparable) || is_array($value) || is_object($comparable) || is_object($value)) {
            return false;
        }

        if (preg_match("/^$comparable/", $value)) {
            return true;
        }

        return false;
    }

    /**
     * End with
     *
     * @param mixed $value
     * @param string $comparable
     *
     * @return bool
     */
    public function endWith($value, $comparable)
    {
        if (is_array($comparable) || is_array($value) || is_object($comparable) || is_object($value)) {
            return false;
        }

        if (preg_match("/$comparable$/", $value)) {
            return true;
        }

        return false;
    }

    /**
     * Match with pattern
     *
     * @param mixed $value
     * @param string $comparable
     *
     * @return bool
     */
    public function match($value, $comparable)
    {
        if (is_array($comparable) || is_array($value) || is_object($comparable) || is_object($value)) {
            return false;
        }

        $comparable = trim($comparable);
        if (preg_match("/^$comparable$/", $value)) {
            return true;
        }
        return false;
    }

    /**
     * Contains substring in string
     *
     * @param string $value
     * @param string $comparable
     *
     * @return bool
     */
    public function contains($value, $comparable)
    {
        return (strpos($value, $comparable) !== false);
    }
}