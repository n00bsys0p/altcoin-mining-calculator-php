<?php

namespace n00bsys0p;

/**
 * Cache creation class
 *
 * Generates a new instance of the app cache
 * based on the application's configured cache
 * directory
 */
class Cache
{
    public static function init()
    {
        $cache = new \Gregwar\Cache\Cache;;
        $cache->setCacheDirectory(CACHE_DIR);
        return $cache;
    }
}
