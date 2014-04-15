<?php

namespace n00bsys0p;

require_once('interfaces/ExplorerInterface.php');
require_once('CachingHttpClient.php');

/**
 * Abe Explorer API class
 *
 * A class that allows interaction with an Abe API, used
 * for the retrieval of cryptocurrency network information.
 *
 * @copyright Hirocoin Developers 2014
 * @version 0.1
 * @author Alex Shepherd (n00bsys0p) <n00b@n00bsys0p.co.uk>
 */
abstract class AbeExplorer implements ExplorerInterface
{
    protected $url = NULL;
    protected $chain = NULL;
    protected $client = NULL;
    protected $block_reward = 0;

    /**
     * Constructor
     *
     * @param array $config The application configuration array
     */
    public function __construct($config)
    {
        $this->url = $config['url'];
        $this->chain = $config['chain'];
        $this->client = new CachingHttpClient;

        $reward = $this->getBlockValue($this->getBlockHeight());
        $this->setBlockReward($reward);
    }

    /**
     * Get the current network difficulty
     *
     * Retrieve the current network difficult from the configured
     * Abe-based explorer.
     */
    public function getDifficulty()
    {
        $url = $this->_constructUrl('getdifficulty');
        $diff = $this->client->get($url, 'diff.dat', array('max-age' => CACHE_BLOCK_TIMEOUT));

        return $diff;
    }

    /**
     * Set the block reward.
     *
     * Set the local class variable's current block reward. You must
     * implement the abstract function getBlockValue($nHeight) for
     * any new coin you add to this app.
     *
     * @param integer $nHeight The block height for which to find the reward
     */
    public function setBlockReward($nHeight)
    {
        $this->block_reward = $this->getBlockValue($nHeight);
    }

    /**
     * Return the current block reward
     */
    public function getBlockReward()
    {
        return $this->block_reward;
    }

    /**
     * Return the current block value for any given subsidy algorithm.
     *
     * Must be implemented for any coin. Port from the original coin's
     * subsidy function code, leaving out the transaction fees.
     *
     * @param integer $nHeight The block height for which to check the current reward.
     */
    abstract public function getBlockValue($nHeight);

    /**
     * Get the current block height.
     *
     * Cached for the selected coin's estimated block time as configured
     * in config/cache.yml
     */
    public function getBlockHeight()
    {
        $url = $this->_constructUrl('getblockheight');
        $height = $this->client->get($url, 'height.dat', array('max-age' => CACHE_BLOCK_TIMEOUT));

        return $height;
    }

    /**
     * Construct an Abe API URL by passing a feature. This may contain
     * multiple URI segments, separated by a /
     */
    protected function _constructUrl($feature)
    {
        $full_url = $this->url . '/chain/' . $this->chain . '/q/' . $feature;

        return $full_url;
    }
}
