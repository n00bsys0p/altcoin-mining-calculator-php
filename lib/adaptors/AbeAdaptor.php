<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/adaptors/BaseAdaptor.php');
require_once(APP_DIR . '/lib/adaptors/exceptions/AbeException.php');
require_once(APP_DIR . '/lib/CachingHttpClient.php');

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
class AbeAdaptor extends BaseAdaptor
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
        $this->sanityCheck($config);

        $this->url = $config['url'];
        $this->chain = $config['chain'];
        $this->client = new CachingHttpClient;
        $this->subsidyFunction = new $config['subsidy_function'];

        $reward = $this->getBlockValue($this->getBlockHeight());
        $this->setBlockReward($reward);
    }

    /**
     * Get the current network difficulty
     *
     * Retrieve the current network difficult from the configured
     * Abe-based explorer.
     *
     * @return float
     */
    public function getDifficulty()
    {
        $url = $this->_constructUrl('getdifficulty');
        $diff = $this->client->get($url, 'diff.dat', array('max-age' => CACHE_BLOCK_TIMEOUT));

        if($diff == 0)
            throw new AbeException('Detected diff at 0. Please try again later.');

        return $diff;
    }

    /**
     * Set the block reward.
     *
     * Set the local class variable's current block reward. You must
     * implement the abstract function getBlockValue($nHeight) for
     * any new coin you add to this app.
     *
     * @param  integer $nHeight The block height for which to find the reward
     */
    public function setBlockReward($nHeight)
    {
        $this->block_reward = $this->getBlockValue($nHeight);
    }

    /**
     * Return the current block reward
     *
     * @return integer
     */
    public function getBlockReward()
    {
        if($this->block_reward <= 0)
            throw new AbeException('Block reward detected <= 0, please try again later.');

        return $this->block_reward;
    }

    /**
     * Return the current block value for any given subsidy algorithm.
     *
     * @param integer $nHeight The block height for which to check the current reward.
     */
    public function getBlockValue($nHeight)
    {
        return $this->subsidyFunction->getBlockValue($nHeight);
    }

    /**
     * Get the current block height.
     *
     * Cached for the selected coin's estimated block time as configured
     * in config/cache.yml
     *
     * @return integer
     */
    public function getBlockHeight()
    {
        $url = $this->_constructUrl('getblockcount');
        $height = $this->client->get($url, 'height.dat', array('max-age' => CACHE_BLOCK_TIMEOUT));

        if($height < 1)
            throw new AbeException('Current block height detected < 1. Please try again later.');

        return $height;
    }

    /**
     * Perform a sanity check on a config array
     *
     * Throws an exception if anything's not right
     */
    protected function sanityCheck($config)
    {
        if(!isset($config['url']) || !isset($config['chain']))
            throw new \Exception('You must set the url and chain options in the adaptor configuration file.');
    }

    /**
     * Make an Abe API URL from a "feature"
     *
     * Construct an Abe API URL by passing a feature. The feature may
     * contain multiple URI segments, separated by a /.
     *
     * @param  string $feature The Abe API feature to acess, including any relevant parameters
     * @return string
     */
    protected function _constructUrl($feature)
    {
        $full_url = $this->url . '/chain/' . $this->chain . '/q/' . $feature;

        return $full_url;
    }
}
