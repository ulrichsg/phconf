<?php

namespace Ulrichsg\PhConf;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class PhConf
{
    const DEFAULT_DELIMITER = ':';

    const INI_WITH_SECTIONS = 1;

    private $store;
    private $fileReader;

    public function __construct()
    {
        $this->store = new Store(self::DEFAULT_DELIMITER);
        $this->fileReader = new FileReader(new Filesystem(new Local('/')));
    }

    public function get($key)
    {
        return $this->store->get($key);
    }

    public function set($key, $value)
    {
        $this->store->set($key, $value);
        return $this;
    }

    public function env()
    {
        $this->store->merge($_ENV);
        return $this;
    }

    public function file($path, $options = 0)
    {
        $data = $this->fileReader->read($path, $options);
        $this->store->merge($data);
        return $this;
    }

    public function defaults(array $values)
    {
        $this->store->merge($values, Store::MERGE_UNDER);
        return $this;
    }
}
