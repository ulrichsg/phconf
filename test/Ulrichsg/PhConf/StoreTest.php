<?php

namespace Ulrichsg\PhConf;

class StoreTest extends \PHPUnit_Framework_TestCase
{
    public function testGetExistingValue()
    {
        $store = new Store(':');
        $store->set('foo', 'bar');
        self::assertEquals('bar', $store->get('foo'));
    }

    public function testGetNonexistingValue()
    {
        $store = new Store(':');
        self::assertNull($store->get('foo'));
    }

    public function testSetNewValue()
    {
        $store = new Store(':');
        $store->set('foo:bar:baz', 'quux');
        self::assertEquals('quux', $store->get('foo:bar:baz'));
    }

    public function testSetExistingValue()
    {
        $store = new Store(':');
        $store->set('foo:bar:baz', 'quux');
        $store->set('foo:bar:baz', 'frob');
        self::assertEquals('frob', $store->get('foo:bar:baz'));
    }

    public function testGetSubtree()
    {
        $store = new Store(':');
        $store->set('foo:bar:baz', 'quux');
        $store->set('foo:bar:biz', 'frob');
        self::assertEquals(['baz' => 'quux', 'biz' => 'frob'], $store->get('foo:bar'));
    }

    public function dataMerge()
    {
        return [
            [Store::MERGE_OVER, 'quux'],
            [Store::MERGE_UNDER, 'baz']
        ];
    }

    /** @dataProvider dataMerge */
    public function testMerge($mode, $newBarValue)
    {
        $store = new Store(':');
        $store->set('foo:bar', 'baz');
        $mergeData = [
            'foo' => [
                'bar' => 'quux',
                'fizz' => 'buzz'
            ]
        ];
        $store->merge($mergeData, $mode);
        self::assertEquals($newBarValue, $store->get('foo:bar'));
        self::assertEquals('buzz', $store->get('foo:fizz'));
    }
}
