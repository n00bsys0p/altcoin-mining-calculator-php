<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/adaptors/BaseAdaptor.php');
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

    // The subsidy function provider
    protected $subsidyFunction = NULL;

    // Message thrown to protect sensitive information from being leaked.
    protected $rpcConnectError = 'Unable to connect to RPC server. Contact server administrator. ';

    /**
     * Constructor
     *
     * Sanity checks the configuration and initialises
     * the RPC client to use JSON RPC spec 1.0.
     */
    public function __construct($config)
    {
        $this->sanityCheck($config);

        $user = $config['user'];
        $pass = $config['pass'];
        $host = $config['host'];
        $port = $config['port'];

        $url = "http://$user:$pass@$host:$port";

        try {
            $this->client = \Tivoka\Client::connect($url);
            $this->client->useSpec('1.0'); // Bitcoin et al use JSON RPC spec 1.0
            $this->subsidyFunction = new $config['subsidy_function'];
        } catch(\Exception $e) {
            throw new RPCException($this->rpcConnectError);
        }
    }

    public function getBlockReward()
    {
        $nHeight = $this->getBlockHeight();

        $blockHash = $this->getBlockHash($nHeight);
        $nBits = hexdec($this->getBlockBits($blockHash));

        return $this->subsidyFunction->getBlockValue($nHeight, $nBits);
    }

    /**
     * Return the current block height
     *
     * @return integer
     */
    public function getBlockHeight()
    {
        $height = $this->call('getblockcount');
        return $height;
    }

    /**
     * Return the current network block difficulty
     *
     * @return float
     */
    public function getDifficulty()
    {
        $diff = $this->call('getdifficulty');
        return $diff;
    }

    public function getBlockHash($nHeight)
    {
        $response = $this->call('getblockhash', array($nHeight));
        return $response;
    }

    public function getBlockBits($blockHash)
    {
        $response = $this->call('getblock', array($blockHash));
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
    protected function call($method, $args = array())
    {
        try {
            $response = $this->client->sendRequest($method, $args);

            return $response->result;
        } catch(\Tivoka\Exception\ConnectionException $e) {
            // Protect against sensitive data leakage
            throw new RPCException($this->rpcConnectError);
        } catch(\Exception $e) {
            throw new RPCException($e->getMessage());
        }
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
            throw new \Exception('To use an RpcAdaptor, you must set user, pass, host, port and subsidy_function in adaptor.yml: ' . $msg);
    }
}
