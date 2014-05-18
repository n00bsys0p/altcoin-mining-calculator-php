<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Quebecoin's subsidy function
 */
class QuebecoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Ported directly from Quebecoin source code, ignoring txfees
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 26; // Original block reward

        if($nHeight == 1)
            $nSubsidy = 21000000;
        elseif($nHeight >= 2 && $nHeight <= 24) // 0 Reward blocks to allow DarkGravityWave to start before distributing QBC.
            $nSubsidy = 0;
        elseif($nHeight >= 25 && $nHeight <= 48) // 1 Hour timeframe with 1 QBC block reward to allow miners to set up and be more fair to all.
            $nSubsidy = 1;
        elseif($nHeight >= 49 && $nHeight <= 72) // 1 Hour timeframe with small block reward to allow miners to set up and be more fair to all.
            $nSubsidy = 4;
        elseif($nHeight >= 73 && $nHeight <= 96) // 1 Hour timeframe with half block reward to allow miners to set up and be more fair to all.
            $nSubsidy = 13;

        // Subsidy is cut in half every 420480 blocks, which will occur approximately every 4 years
        $nSubsidy >>= ($nHeight / 420480); // Quebecoin: 420480 blocks in ~2 years

        return $nSubsidy;
    }
}
