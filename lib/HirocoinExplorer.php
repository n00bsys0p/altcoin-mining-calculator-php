<?php

namespace n00bsys0p;

require_once('lib/AbeExplorer.php');

class HirocoinExplorer extends AbeExplorer
{
    public function __construct($config)
    {
        parent::__construct($config);

        // Get nHeight // == Current block height
        // nSubsidy = 400 * COIN // == 100000000
        // nSubsidy >>= (nHeight / 840000);
    }

    public function getBlockValue($nHeight)
    {
        $nSubsidy = 400; // Original block reward
        $nSubsidy >>= ($nHeight / 840000);

        return $nSubsidy;
    }

    /*public function getDifficulty()
    {
        try {
            $response = $this->client->getorCreate('diff.dat', array('max-age' => CACHE_BLOCK_TIMEOUT), function($filename) {
                $url = 'http://hiro.p2pool.n00bsys0p.co.uk:9408/global_stats';
                $json = (string) $this->client->get($url)->getBody();
                $json = json_decode($json);
                $diff = $json->network_block_difficulty;
                return $diff;
            });
        } catch(\Exception $e) {
            return 0;
        }

        return $response;
    }*/
}
