<?php

namespace Ulrichsg\PhConf;

class Store
{
    const MERGE_OVER = 0;   // Values being merged in overwrite existing values with the same keys
    const MERGE_UNDER = 1;  // Values being merged in do not overwrite existing values with the same keys

    private $data = [];

    private $delimiter;

    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function has($key)
    {
        $data = $this->data;
        $keyParts = $this->splitKey($key);
        foreach ($keyParts as $keyPart) {
            if (!array_key_exists($keyPart, $data)) {
                return false;
            }
            $data = $data[$keyPart];
        }
        return true;
    }

    public function get($key)
    {
        $data = $this->data;
        $keyParts = $this->splitKey($key);
        foreach ($keyParts as $keyPart) {
            if (!array_key_exists($keyPart, $data)) {
                return null;
            }
            $data = $data[$keyPart];
        }
        return $data;
    }

    public function set($key, $value)
    {
        $keyParts = $this->splitKey($key);
        $leafName = array_pop($keyParts);
        $data =& $this->data;
        foreach ($keyParts as $keyPart) {
            if (!array_key_exists($keyPart, $data) || !is_array($data[$keyPart])) {
                $data[$keyPart] = [];
            }
            $data =& $data[$keyPart];
        }
        $data[$leafName] = $value;
    }

    public function merge(array $array, $mode = self::MERGE_OVER)
    {
        $this->mergeAtPath($array, $mode, []);
    }

    private function mergeAtPath(array $array, $mode, array $path)
    {
        foreach ($array as $key => $value) {
            array_push($path, $key);
            if (is_array($value)) {
                $this->mergeAtPath($value, $mode, $path);
            } else {
                $fullKey = implode($this->delimiter, $path);
                if ($mode === self::MERGE_OVER || !$this->has($fullKey)) {
                    $this->set($fullKey, $value);
                }
            }
            array_pop($path);
        }
    }

    private function splitKey($key)
    {
        return explode($this->delimiter, $key);
    }
}
