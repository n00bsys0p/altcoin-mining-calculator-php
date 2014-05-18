<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Muniti's subsidy function
 */
class HighFiveCoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Ported directly from H5C source code, ignoring txfees
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 55;

        if ($nHeight == 1)
            return 538555;
        elseif ($nHeight < 500)
            return 1;
        elseif ($nHeight < 750)
            return 5;

        // How many weeks since launch
        $weeksElapsed = $nHeight / 13440;

        // 1% compounding subsidy reduction every 2 weeks
        $nSubsidy *= pow(0.99, $weeksElapsed);

        return $nSubsidy;
    }
}
