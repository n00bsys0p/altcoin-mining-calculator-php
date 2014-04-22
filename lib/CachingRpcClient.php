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
class CachingRpcClient
{
    protected $client = NULL;
    protected $cache = NULL;

    /**
     * Constructor
     *
     * Initialises the internal app cache and the
     * HTTP client to use for requests.
     */
    public function __construct($user, $pass, $host, $port)
    {
        $this->url = "http://$user:$pass@$host:$port";
        $this->cache = Cache::init();
    }

    /**
     * Pass details to the real caching function, and return a blank string on an
     * empty response.
     *
     * This simply wraps the protected _getOrCache function
     * for ease of use.
     *
     * @param string $method         The URL to get or cache
     * @param string $cache_filename The name of the cache file to save to
     * @param array  $cache_opts     An optional array of options to pass to the cache
     * @param array  $method_args    An optional array of options to append to the method
     */
    public function get($method, $cache_filename, $cache_opts = array(), $method_args = array())
    {
        return (empty($method)) ? '' : $this->getOrCache($method, $cache_filename, $cache_opts, $method_args);
    }

    /**
     * Get or cache a page via RPC.
     *
     * This uses phpfastcache and Tivoka to retrieve some data from either
     * the local cache or the local RPC server, based on parameters passed.
     *
     * It will return $default_result, whatever that is set to on a failure.
     *
     * @param string $method         The URL to get or cache
     * @param string $cache_filename The name of the cache file to save to
     * @param array  $cache_opts     An array of options to pass to the cache
     * @param array  $method_args    An array of options to append to the method
     */
    protected function getOrCache($method, $cache_filename, $cache_opts, $method_args)
    {
        //error_log('RPCClient ('.$cache_filename.'): ' . $method . ' (' . str_replace("\n", '', print_r($method_args, TRUE)) . ')');

        try {
            $result = $this->cache->getOrCreate($cache_filename, $cache_opts, function($filename) use($method, $method_args) {
                $url = $this->url;
                $client = \Tivoka\Client::connect($url);
                $client->useSpec('1.0');
                // The data needs to be sensibly serialised for storage
                return serialize($client->sendRequest($method, $method_args)->result);
            });
        }catch(\Exception $e) {
            throw new RPCException($e->getMessage());
        }

        // Now we can safely unserialize it again
        return unserialize($result);
    }
}
