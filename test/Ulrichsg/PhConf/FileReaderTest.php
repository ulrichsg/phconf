<?php

namespace Ulrichsg\PhConf;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Ulrichsg\PhConf\Error\FileNotFound;

class FileReaderTest extends \PHPUnit_Framework_TestCase
{

    public function dataIniFile()
    {
        return [
            ['/a/b.ini'],
            ['/a/b.cfg']
        ];
    }

    /** @dataProvider dataIniFile */
    public function testIniFile($path)
    {
        $contents = <<<EOF
[parameters]
foo = bar
baz = quux

narf[] = zort
narf[] = poit
EOF;
        $data = ['foo' => 'bar', 'baz' => 'quux', 'narf' => [0 => 'zort', 1 => 'poit']];

        $fs = new Filesystem(new MemoryAdapter());
        $fs->put($path, $contents);

        $reader = new FileReader($fs);
        self::assertEquals($data, $reader->read($path));
    }

    public function testIniFileWithSections()
    {
        $contents = <<<EOF
[one]
foo = bar

[two]
baz = quux
EOF;
        $data = ['one' => ['foo' => 'bar'], 'two' => [ 'baz' => 'quux']];
        $path = '/a/b.ini';

        $fs = new Filesystem(new MemoryAdapter());
        $fs->put($path, $contents);

        $reader = new FileReader($fs);
        self::assertEquals($data, $reader->read($path, PhConf::INI_WITH_SECTIONS));
    }

    public function dataJsonFile()
    {
        return [
            ['/a/b.json'],
            ['/a/b.cfg']
        ];
    }

    /** @dataProvider dataJsonFile */
    public function testJsonFile($path)
    {
        $contents = <<<EOF
{
    "foo": "bar",
    "baz": {
        "quux": "frob",
        "fizz": "buzz"
    },
    "narf": [
        "zort",
        "poit"
    ]
}
EOF;
        $data = ['foo' => 'bar', 'baz' => ['quux' => 'frob', 'fizz' => 'buzz'], 'narf' => [0 => 'zort', 1 => 'poit']];

        $fs = new Filesystem(new MemoryAdapter());
        $fs->put($path, $contents);

        $reader = new FileReader($fs);
        self::assertEquals($data, $reader->read($path));
    }

    public function dataYamlFile()
    {
        return [
            ['/a/b.yml'],
            ['/a/b.cfg']
        ];
    }

    /** @dataProvider dataYamlFile */
    public function testYamlFile($path)
    {
        $contents = <<<EOF
foo: bar
baz:
  quux: frob
  fizz: buzz
narf:
  - zort
  - poit
EOF;
        $data = ['foo' => 'bar', 'baz' => ['quux' => 'frob', 'fizz' => 'buzz'], 'narf' => [0 => 'zort', 1 => 'poit']];

        $fs = new Filesystem(new MemoryAdapter());
        $fs->put($path, $contents);

        $reader = new FileReader($fs);
        self::assertEquals($data, $reader->read($path));
    }

    public function testFileNotFound()
    {
        $fs = new Filesystem(new MemoryAdapter());
        $reader = new FileReader($fs);
        $this->expectException(FileNotFound::class);
        $reader->read('/a/b.cfg');
    }
}
