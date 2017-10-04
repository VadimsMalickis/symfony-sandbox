<?php


namespace TreeBundle\SimpleNumberCache;


use TreeBundle\SimpleNumberCache\Exception\KeyNotFound;

class SimpleNumberCache
{
    /** @var int[] */
    private $cache = [];

    /**
     * @param string $key
     */
    public function invalidate($key)
    {
        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function store($key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     * @param $key
     * @return int
     * @throws KeyNotFound
     */
    public function getFromCache($key)
    {
        if (!$this->exists($key)) {
            throw new KeyNotFound('Key not found');
        }
        return $this->cache[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        if (array_key_exists($key, $this->cache)) {
            return true;
        }
        return false;
    }
}