<?php


namespace TreeBundle\SimpleNumberCache;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use TreeBundle\SimpleNumberCache\Exception\KeyNotFound;

class SimpleNumberCacheTest extends WebTestCase
{
    public function testCache()
    {
        $cache = new SimpleNumberCache();
        $cache->store('task', 1234);
        $this->assertTrue($cache->exists('task'));
        $this->assertEquals(1234, $cache->getFromCache('task'));
        $cache->invalidate('task');
        $this->assertFalse($cache->exists('task'));

        $cache->store('a', 5);
        $cache->store('b', 4);
        $this->assertEquals(5, $cache->getFromCache('a'));
        $this->assertEquals(4, $cache->getFromCache('b'));
    }

    public function testKeyNotFound()
    {
        $cache = new SimpleNumberCache();
        $this->expectException(KeyNotFound::class);
        $cache->getFromCache('task');
    }
}