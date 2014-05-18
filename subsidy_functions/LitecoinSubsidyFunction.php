<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Hirocoin's subsidy function
 */
class LitecoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Litecoin subsidy function
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 50; // Original block reward

        // Subsidy is cut in half every 840000 blocks, which will occur approximately every 4 years
        $nSubsidy >>= ($nHeight / 840000)
        return $nSubsidy;
    }
}
