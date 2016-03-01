<?php

namespace Ulrichsg\PhConf\Error;

class FileNotFound extends PhConfError
{
    public function __construct($path)
    {
        parent::__construct("File not found: $path");
    }
}
