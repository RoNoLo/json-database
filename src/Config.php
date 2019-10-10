<?php

namespace RoNoLo\Flydb;

/**
 * Config
 *
 * Responsible for storing variables used throughout a Flywheel instance
 */
class Config
{
    protected $options;

    /**
     * Constructor
     *
     * @param array $options Any other configuration options.
     */
    public function __construct(array $options = [])
    {
        // Merge supplied options with the defaults
        $options = array_merge([
            'query_class' => $this->hasAPC() ? CachedQuery::class : Query::class,
            'document_class' => Document::class,
            'json_encode_options' => 0
        ], $options);

        $this->options = $options;
    }

    /**
     * Gets a specific option from the config
     *
     * @param string $name The name of the option to return.
     *
     * @return mixed The value of the option if it exists or null if it doesnt.
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    public function hasAPC()
    {
        return function_exists('apcu_fetch') || function_exists('apc_fetch');
    }
}
