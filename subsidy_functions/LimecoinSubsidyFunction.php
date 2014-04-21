<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Limecoin's subsidy function
 */
class LimecoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Limecoin's block reward is just 100. No subsidy over time. Supply
     * whatever random details you like to the function.
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @param  float   $dDiff   The difficulty of the last block
     * @return float
     */
    public function getBlockValue($nHeight, $dDiff)
    {
        $nSubsidy = 100; // Original block reward

        return $nSubsidy;
    }
}
