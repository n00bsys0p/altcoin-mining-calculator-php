<?php

namespace n00bsys0p;

require_once('interfaces/ExplorerInterface.php');
require_once('CachingHttpClient.php');

abstract class AbeExplorer implements ExplorerInterface
{
    protected $url = NULL;
    protected $chain = NULL;
    protected $client = NULL;
    protected $block_reward = 0;

    public function __construct($config)
    {
        $this->url = $config['url'];
        $this->chain = $config['chain'];
        $this->client = new CachingHttpClient;

        $reward = $this->getBlockValue($this->getBlockHeight());
        $this->setBlockReward($reward);
    }

    public function getDifficulty()
    {
        $url = $this->_constructUrl('getdifficulty');
        $diff = $this->client->get($url, 'diff.dat', array('max-age' => CACHE_BLOCK_TIMEOUT));

        return $diff;
    }

    public function setBlockReward($nHeight)
    {
        $this->block_reward = $this->getBlockValue($nHeight);
    }

    public function getBlockReward()
    {
        return $this->block_reward;
    }

    /**
     * Must be implemented for any coin. Port from the original coin's
     * subsidy function code, leaving out the transaction fees.
     */
    abstract public function getBlockValue($nHeight);

    /**
     * Get the current block height. Cached for the selected coin's
     * estimated block time.
     */
    public function getBlockHeight()
    {
        $url = $this->_constructUrl('getblockheight');
        $height = $this->client->get($url, 'height.dat', array('max-age' => CACHE_BLOCK_TIMEOUT));

        return $height;
    }

/*    protected function _getOrCache($api_uri, $cache_filename, $cache_opts = array(), $default_result = 0)
    {
        try {
            $result = $this->cache->getOrCreate($cache_filename, $cache_opts, function($filename) use($api_uri) {
                $url = $this->_constructUrl($api_uri);
                $response = (string) $this->client->get($url)->getBody();
                return $response;
            });
        } catch(\Exception $e) {
            return $default_result;
        }

        return $result;
    }*/

    protected function _constructUrl($feature)
    {
        $full_url = $this->url . '/chain/' . $this->chain . '/q/' . $feature;

        return $full_url;
    }
}
