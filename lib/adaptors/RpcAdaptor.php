<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/adaptors/BaseAdaptor.php');
require_once(APP_DIR . '/lib/CachingRpcClient.php');
require_once(APP_DIR . '/lib/adaptors/exceptions/RPCException.php');

/**
 * RPC adaptor class
 *
 * This is a base class for any coin to extend. Using an RPC
 * adaptor will have a lot better performance than an Abe
 * adaptor, but requires you to be running a local instance
 * of the cryptocoin daemon.
 *
 * To implement this class, you need to implement getBlockValue
 * to return the current block reward. This can usually be trivially
 * ported from the coin's source code.
 */
class RpcAdaptor extends BaseAdaptor
{
    // JSON RPC Client capable of RPC spec 1.0
    protected $client = NULL;

    // The coin to which this RPC adaptor pertains
    protected $coin = NULL;

    // The subsidy function provider
    protected $subsidyFunction = NULL;

    // Message thrown to protect sensitive information from being leaked.
    protected $rpcConnectError = 'Unable to connect to RPC server. Contact server administrator. ';

    // Used widely through the class
    protected $cacheBlockOptions = array('max-age' => CACHE_BLOCK_TIMEOUT);
    /**
     * Constructor
     *
     * Sanity checks the configuration and initialises the Caching RPC Client
     */
    public function __construct($config)
    {
        $this->sanityCheck($config);

        // Use the coin name from the SubsidyFunction class name - used for caching.
        $this->coin = str_replace('SubsidyFunction', '', end(explode('\\', $config['subsidy_function'])));

        $user = $config['user'];
        $pass = $config['pass'];
        $host = $config['host'];
        $port = $config['port'];

        try {
            $this->client = new CachingRpcClient($user, $pass, $host, $port);
            $this->subsidyFunction = new $config['subsidy_function'];
        } catch(\Exception $e) {
            // Protect against data leakage
            throw new RPCException($this->rpcConnectError . 'From: ' . $this->coin . ' (Connect)');
        }
    }

    public function getBlockReward()
    {
        $nHeight = $this->getBlockHeight();
        $dDiff = $this->getDifficulty();

        return $this->subsidyFunction->getBlockValue($nHeight, $dDiff);
    }

    /**
     * Return the current block height
     *
     * @return integer
     */
    public function getBlockHeight()
    {
        $command = 'getblockcount';
        $height = $this->call($command, 'height_' . $this->coin, $this->cacheBlockOptions, array());
        return $height;
    }

    /**
     * Return the current network block difficulty
     *
     * @return float
     */
    public function getDifficulty()
    {
        /**
         * More complex method but more accurate number for
         * difficulty than reported by getdifficulty
         */
        $nHeight = $this->getBlockHeight();

        $blockHash = $this->getBlockHash($nHeight);
        $nBits = $this->getBlockBits($blockHash);

        $dDiff = $this->convertBlockBitsToDiff($nBits);

        return $dDiff;
        //return $this->call('getdifficulty');
    }

    public function getBlockHash($nHeight)
    {
        $filename = 'hash_' . $this->coin;
        $cmd_args = array($nHeight);

        $response = $this->call('getblockhash', $filename, $this->cacheBlockOptions, $cmd_args);

        return $response;
    }

    public function getBlockBits($blockHash)
    {
        $filename = 'block_' . $this->coin;
        $args = array($blockHash);
        $response = $this->call('getblock', $filename, $this->cacheBlockOptions, $args);

        return $response['bits'];
    }

    /**
     * Convert a block's bits hex string to an actual diff.
     *
     * @param string $nBits The ['bits'] parameter for a block.
     * @return float
     */
    public static function convertBlockBitsToDiff($nBits)
    {
        $nBits = hexdec($nBits);

        $dDiff = (double) hexdec('0x0000ffff') / (double) ($nBits & hexdec('0x00ffffff'));

        $nShift = ($nBits >> 24) & hexdec('0xff');
        while($nShift < 29)
        {
            $dDiff *= 256.0;
            $nShift++;
        }
        while($nShift > 29)
        {
            $dDiff /= 256.0;
            $nShift--;
        }

        return $dDiff;
    }

    /**
     * Call a method on the RPC client
     *
     * Simple wrapper for sendRequest which cleans any
     * errors that may leak sensitive information.
     *
     * @param  string $method The method to call on the RPC server
     * @param  array  $args   An array of arguments to pass to the method
     * @return mixed
     */
    protected function call($method, $cache_filename, $cache_opts = array(), $args = array())
    {
        try {
            $response = $this->client->get($method, $cache_filename, $cache_opts, $args);
        } catch(RPCException $e) {
            // Protect against sensitive data leakage
            throw new RPCException($this->rpcConnectError . 'From: ' . $this->coin . ' (Call)');
        } catch(\Exception $e) {
            throw new RPCException($e->getMessage());
        }

        return $response;
    }

    /**
     * Sanity check the config passed
     *
     * Throw an exception if any config item is missing.
     */
    protected function sanityCheck($config)
    {
        if((!isset($config['user'])) || (!isset($config['pass'])) ||
            (!isset($config['host'])) || (!isset($config['port'])) ||
            (!isset($config['subsidy_function'])))
            throw new \Exception('To use an RpcAdaptor, you must set user, pass, host, port and subsidy_function for the coin in adaptors.yml: ' . $msg);
    }
}
