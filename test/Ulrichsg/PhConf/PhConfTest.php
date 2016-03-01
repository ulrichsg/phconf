<?php

namespace Ulrichsg\PhConf;

class PhConfTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigFromEnv()
    {
        $_ENV['foo'] = 'bar';
        $conf = new PhConf();
        $conf->env();
        self::assertEquals('bar', $conf->get('foo'));
    }

    public function testConfigFromIniFile()
    {

    }

    public function testUseDefaultWhenNoOtherValueGiven()
    {
        $conf = new PhConf();
        $conf
            ->set('foo', 'bar')
            ->defaults(['foo' => 'baz', 'get' => 'rekt']);

        self::assertEquals('bar', $conf->get('foo'));
        self::assertEquals('rekt', $conf->get('get'));
    }

    public function testFlattenDefaultValues()
    {
        $conf = new PhConf();
        $conf->defaults(['foo' => ['bar' => 'baz']]);

        self::assertEquals('baz', $conf->get('foo:bar'));
    }
}
