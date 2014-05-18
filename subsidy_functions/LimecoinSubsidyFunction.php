<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Limecoin's subsidy function
 */
class LimecoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Limecoin's block reward is just 100. No subsidy over time. You don't
     * need to supply any parameters.
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return float
     */
    public function getBlockValue($nHeight = NULL)
    {
        $nSubsidy = 100; // Original block reward

        return $nSubsidy;
    }
}
