<?php

namespace n00bsys0p;

require_once('CachingHttpClient.php');

class Exchange
{
    protected $name = NULL;
    protected $client = NULL;
    protected $url = NULL;
    protected $json_key = NULL;

    public function __construct($config)
    {
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

    public function getBtcRate()
    {
        $cache_opts = array('max-age' => CACHE_EXCHANGE_TIMEOUT); // 15 minutes cache BTC to altcoin rate
        $response = $this->client->get($this->url, 'btcrate_' . $this->name . '.dat', $cache_opts);

        $response = $this->_getJsonValue($response, $this->json_key);

        return $response;
    }

    private function _getJsonValue($json_string, $key_string)
    {
        $keys_ary = explode('.', $key_string);

        $result = json_decode($json_string);

        foreach($keys_ary as $key) {
            $result = (is_array($result)) ? $result[$key] : $result->$key;
        }

        return $result;
    }
}
