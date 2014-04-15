<?php

namespace n00bsys0p;

require_once('lib/AbeExplorer.php');

/**
 * Sample Abe Explorer extension for Hirocoin
 *
 * All currencies which will use an Abe explorer as a data
 * source require a new class to be implemented, which extends
 * AbeExplorer as we need to know the subsidy function for the
 * coin. This can usually fairly simply be ported directly from
 * the coin's subsidy function code. You need to implement the
 * function getBlockValue($nHeight), and have it respond with an
 * integer that represents the current block reward.
 *
 * This class also has (commented out), an example of using this
 * class extension to use a customised method for detecting the
 * network block difficulty by using a P2Pool node.
 */
class HirocoinExplorer extends AbeExplorer
{
    /**
     * Ported directly from Hirocoin source code, ignoring txfees
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 400; // Original block reward
        $nSubsidy >>= ($nHeight / 840000);

        return $nSubsidy;
    }

    /**
     * Sample function.
     *
     * Demonstrates using a P2Pool node to get diff instead of
     * block explorer
     */
    /*public function getDifficulty()
    {
        $cache_opts = array('max-age' => CACHE_BLOCK_TIMEOUT);
        $response = $this->client->get('http://hiro.p2pool.n00bsys0p.co.uk:9408/global_stats', 'diff.dat', $cache_opts, 0);
        $json = json_decode($response);
        if($json)
            $diff = $json->network_block_difficulty;
        else
            $diff = 0;

        return $diff
    }*/
}
