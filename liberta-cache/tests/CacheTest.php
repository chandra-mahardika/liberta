<?php

declare(strict_types=1);

namespace Liberta\Cache\Tests;

use PHPUnit\Framework\TestCase;
use Liberta\Cache\ArrayCache;
use Liberta\Cache\FileCache;

class CacheTest extends TestCase
{
    public function testArrayCacheSetAndGet(): void
    {
        $cache = new ArrayCache();

        $this->assertTrue($cache->set('name', 'Chandra'));
        $this->assertEquals('Chandra', $cache->get('name'));
        $this->assertNull($cache->get('missing'));
        $this->assertEquals('default', $cache->get('missing', 'default'));
    }

    public function testArrayCacheDelete(): void
    {
        $cache = new ArrayCache();
        $cache->set('key', 'value');

        $this->assertTrue($cache->delete('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testArrayCacheHas(): void
    {
        $cache = new ArrayCache();
        $cache->set('key', 'value');

        $this->assertTrue($cache->has('key'));
        $this->assertFalse($cache->has('missing'));
    }

    public function testArrayCacheFlush(): void
    {
        $cache = new ArrayCache();
        $cache->set('a', 1);
        $cache->set('b', 2);

        $cache->flush();

        $this->assertNull($cache->get('a'));
        $this->assertNull($cache->get('b'));
    }

    public function testArrayCacheIncrement(): void
    {
        $cache = new ArrayCache();
        $cache->set('counter', 0);

        $this->assertEquals(1, $cache->increment('counter'));
        $this->assertEquals(3, $cache->increment('counter', 2));
    }

    public function testArrayCacheDecrement(): void
    {
        $cache = new ArrayCache();
        $cache->set('counter', 10);

        $this->assertEquals(9, $cache->decrement('counter'));
    }

    public function testArrayCacheRemember(): void
    {
        $cache = new ArrayCache();
        $callCount = 0;

        $result = $cache->remember('key', 60, function () use (&$callCount) {
            $callCount++;
            return 'computed';
        });

        $this->assertEquals('computed', $result);
        $this->assertEquals(1, $callCount);

        $result2 = $cache->remember('key', 60, function () use (&$callCount) {
            $callCount++;
            return 'computed';
        });

        $this->assertEquals('computed', $result2);
        $this->assertEquals(1, $callCount);
    }

    public function testArrayCacheGetMultiple(): void
    {
        $cache = new ArrayCache();
        $cache->set('a', 1);
        $cache->set('b', 2);

        $results = $cache->getMultiple(['a', 'b', 'c']);

        $this->assertEquals(1, $results['a']);
        $this->assertEquals(2, $results['b']);
        $this->assertNull($results['c']);
    }

    public function testFileCacheSetAndGet(): void
    {
        $cache = new FileCache(sys_get_temp_dir() . '/liberta_test_cache');

        $cache->set('test_key', 'test_value', 60);
        $this->assertEquals('test_value', $cache->get('test_key'));

        $cache->flush();
    }

    public function testFileCacheExpired(): void
    {
        $cache = new FileCache(sys_get_temp_dir() . '/liberta_test_cache');

        $cache->set('expires', 'now', -1);
        $this->assertNull($cache->get('expires'));

        $cache->flush();
    }
}
