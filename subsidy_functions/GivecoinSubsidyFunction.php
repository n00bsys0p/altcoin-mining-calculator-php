<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Givecoin's subsidy function
 */
class GivecoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Ported directly from Givecoin source code, ignoring txfees
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 0;
        if ($nHeight <= 5) {    // For Each 5 blocks will have 0.5M coins
           $nSubsidy = 5000000;
        }
        else {
           $nSubsidy = 1000;
        }
        // Subsidy is cut in half every 250,000 blocks, which will occur approximately every .5 year
        $nSubsidy >>= ($nHeight / 250000); // Givecoin: 250k blocks in ~.5 years

        if ($nSubsidy < 1) $nSubsidy = 1;  // Minimum Number of Coin = 1
        return $nSubsidy;
    }
}
