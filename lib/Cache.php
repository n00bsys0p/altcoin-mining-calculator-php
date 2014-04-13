<?php

namespace n00bsys0p;

class Cache
{
    public static function init()
    {
        $cache = new \Gregwar\Cache\Cache;;
        $cache->setCacheDirectory(CACHE_DIR);
        return $cache;
    }
}
