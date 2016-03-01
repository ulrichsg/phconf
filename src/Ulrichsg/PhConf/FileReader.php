<?php

namespace Ulrichsg\PhConf;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Ulrichsg\PhConf\Error\FileNotFound;
use Ulrichsg\PhConf\Error\ParseError;

class FileReader
{
    const TYPE_YML = 'yml';
    const TYPE_JSON = 'json';
    const TYPE_INI = 'ini';

    private $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function read($path, $options = 0)
    {
        try {
            $contents = $this->filesystem->read($path);
        } catch (FileNotFoundException $e) {
            throw new FileNotFound($path);
        }
        $fileType = $this->guessFileType($path, $contents);
        switch ($fileType) {
            case self::TYPE_INI:
                $withSections = $options === PhConf::INI_WITH_SECTIONS;
                $result = parse_ini_string($contents, $withSections);
                if ($result === false) {
                    throw new ParseError("Error parsing $path as INI");
                }
                return $result;
            case self::TYPE_JSON:
                $result = json_decode($contents, true);
                if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                    throw new ParseError("Error parsing $path as JSON: ".json_last_error_msg());
                }
                return $result ?: [];
            case self::TYPE_YML:
                try {
                    return Yaml::parse($contents, true);
                } catch (ParseException $e) {
                    throw new ParseError("Error parsing $path as YAML: ".$e->getMessage());
                }
        }

    }

    private function guessFileType($path, $contents)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        switch ($extension) {
            case 'yml':
            case 'yaml':
                return self::TYPE_YML;
            case 'json':
                return self::TYPE_JSON;
            case 'ini':
                return self::TYPE_INI;
        }
        if ($contents[0] === '{') {
            return self::TYPE_JSON;
        }
        if (preg_match('/^\w+\s*:/', $contents)) {
            return self::TYPE_YML;
        }
        return self::TYPE_INI;
    }
}
