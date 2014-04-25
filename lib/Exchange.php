<?php

namespace n00bsys0p;

require_once('CachingHttpClient.php');
require_once('exceptions/ExchangeException.php');

/**
 * A single Exchange object
 *
 * This represents a single exchange, as found in the exchange
 * config file.
 */
class Exchange
{
    protected $name = NULL;
    protected $coin = NULL;
    protected $client = NULL;
    protected $url = NULL;
    protected $json_key = NULL;

    /**
     * Constructor
     *
     * Construct all the internal objects and variables
     * required to interact with the exchange's public
     * API.
     *
     * @param array $config A single exchange's configuration
     */
    public function __construct($coin, $config)
    {
        $this->coin = $coin;
        $this->client = new CachingHttpClient;
        $this->name = $config['name'];

        $this->json_key = $config['json_key'];
        $this->url = $config['base_url'] . '/' . $config['uri'];

        // Append any necessary get parameters
        if(isset($config['get_params']))
        {
            $this->url .= '?';

            foreach($config['get_params'] as $k => $v)
            {
                $this->url .= urlencode($k) . '=' . urlencode($v);
                $this->url .= '&';
            }

            // Remove the appended &
            $this->url = substr($this->url, 0, strlen($this->url) - 1);
        }
    }

    /**
     * Return the BTC rate for the current coin
     *
     * Use the exchange's public API via HTTP to find out the
     * current rates. Results are cached according to the configured
     * CACHE_EXCHANGE_TIMEOUT
     *
     * @return float
     */
    public function getBtcRate()
    {
        $cache_opts = array('max-age' => CACHE_EXCHANGE_TIMEOUT); // 15 minutes cache BTC to altcoin rate
        $response = $this->client->get($this->url, 'btcrate_' . $this->name . '_' . $this->coin . '.dat', $cache_opts);

        $response = $this->_getJsonValue($response, $this->json_key);

        return $response;
    }

    /**
     * Parse a path from a json string given a serialised key
     *
     * This will split $key_string by delimiter ".", and find
     * the relevant element of the JSON array. No protection
     * against not finding the element though, so make sure your
     * config is right
     *
     * @param  string $json_string The string form of a JSON object
     * @param  string $key_string  A . delimited string denoting nested children of the JSON object
     * @return mixed
     */
    private function _getJsonValue($json_string, $key_string)
    {
        // If something's wrong just enter 0 for now
        if($json_string === 0)
            return 0;

        $keys_ary = explode('.', $key_string);

        $result = json_decode($json_string);

        if($result)
        {
            foreach($keys_ary as $key)
                $result = (is_array($result)) ? $result[$key] : $result->$key;
        }
        else
            throw new ExchangeException('Unable to decode JSON from response: ' . $json_string);

        return $result;
    }
}
