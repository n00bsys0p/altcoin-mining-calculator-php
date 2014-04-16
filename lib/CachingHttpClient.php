<?php

namespace n00bsys0p;

require_once('Cache.php');
require_once('exceptions/CacheException.php');

/**
 * Caching HTTP Client
 *
 * This class is a fairly simple wrapper around Guzzle
 * and the configured caching library.
 */
class CachingHttpClient
{
    protected $client = NULL;
    protected $cache = NULL;

    /**
     * Constructor
     *
     * Initialises the internal app cache and the
     * HTTP client to use for requests.
     */
    public function __construct()
    {
        $this->cache = Cache::init();
        $this->client = new \GuzzleHttp\Client;
    }

    /**
     * Pass details to the real caching function.
     *
     * This simply wraps the protected _getOrCache function
     * for ease of use.
     *
     * @param string $url            The URL to get or cache
     * @param string $cache_filename The name of the cache file to save to
     * @param array  $cache_opts     An optional array of options to pass to the cache
     */
    public function get($url, $cache_filename, $cache_opts = array())
    {
        return $this->_getOrCache($url, $cache_filename, $cache_opts);
    }

    /**
     * Get or cache a page via HTTP.
     *
     * This uses phpfastcache and Guzzle to retrieve some data from either
     * the local cache or the web, based on parameters passed.
     *
     * It will return $default_result, whatever that is set to on a failure.
     *
     * @param string $url            The URL to get or cache
     * @param string $cache_filename The name of the cache file to save to
     * @param array  $cache_opts     An optional array of options to pass to the cache
     */
    protected function _getOrCache($url, $cache_filename, $cache_opts)
    {
        try {
            $result = $this->cache->getOrCreate($cache_filename, $cache_opts, function($filename) use($url) {
                $response = (string) $this->client->get($url)->getBody();
                return $response;
            });
        } catch(\Exception $e) {
            throw new CacheException($e->getMessage());
        }

        return $result;
    }
}
