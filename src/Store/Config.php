<?php

namespace RoNoLo\JsonStorage\Store;

use League\Flysystem\AdapterInterface;

class Config
{
    /** @var AdapterInterface */
    private $adapter;

    private $options = [];

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function getAdapter(): AdapterInterface
    {
        return $this->adapter;
    }

    public function setOption(string $name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}