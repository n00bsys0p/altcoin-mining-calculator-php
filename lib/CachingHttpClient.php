<?php

namespace n00bsys0p;

require_once('Cache.php');

class CachingHttpClient
{
    protected $client = NULL;
    protected $cache = NULL;

    public function __construct()
    {
        $this->cache = Cache::init();
        $this->client = new \GuzzleHttp\Client;
    }

    public function get($url, $cache_filename = NULL, $cache_opts = array(), $default_result = 0)
    {
        return $this->_getOrCache($url, $cache_filename, $cache_opts, $default_result);
    }

    protected function _getOrCache($url, $cache_filename, $cache_opts, $default_result)
    {
        try {
            $result = $this->cache->getOrCreate($cache_filename, $cache_opts, function($filename) use($url) {
                $response = (string) $this->client->get($url)->getBody();
                return $response;
            });
        } catch(\Exception $e) {
            return $default_result;
        }

        return $result;
    }
}
