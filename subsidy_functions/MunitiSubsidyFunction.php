<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Muniti's subsidy function
 */
class MunitiSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Ported directly from Muniti source code, ignoring txfees
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 39;

        if ($nHeight == 1)
            return 24334133;
        elseif ($nHeight < 200)
            return 5;
        elseif ($nHeight < 400)
            return 20;

        // How many weeks since launch
        $weeksElapsed = $nHeight / 13440;

        // 1% compounding subsidy reduction every 2 weeks
        $nSubsidy = $nSubsidy * pow(0.99, $weeksElapsed);

        return $nSubsidy;
    }
}
