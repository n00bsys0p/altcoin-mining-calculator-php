<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * CannabisCoin subsidy function
 */
class CannabisCoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * CannabisCoin's subsidy function ported from GetBlockValue
     *
     * @param  integer $nHeight The block height for which to find the subsidy
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 420;

        $nSubsidy >>= ($nHeight / 100000);

        return $nSubsidy;
    }
}
