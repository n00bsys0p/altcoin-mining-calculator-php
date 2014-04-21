<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Hirocoin's subsidy function
 */
class HirocoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Ported directly from Hirocoin source code, ignoring txfees
     * $dDiff is not required for this subsidy so supply NULL or
     * any other value.
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @param  float   $dDiff   The difficulty of the last block
     * @return float
     */
    public function getBlockValue($nHeight, $dDiff)
    {
        $nSubsidy = 400; // Original block reward
        $nSubsidy >>= ($nHeight / 840000);

        return $nSubsidy;
    }
}
