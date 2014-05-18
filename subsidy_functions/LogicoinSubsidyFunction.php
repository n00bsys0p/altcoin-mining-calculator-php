<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Logicoin subsidy function
 */
class LogicoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Logicoin's subsidy function ported from GetBlockValue
     *
     * @param  integer $nHeight The block height for which to find the subsidy
     * @return float
     */
    public function getBlockValue($nHeight)
    {
        $nSubsidy = 50;

        // Susidy is cut in half every 43200 block, Which is about every 30 days.
        $nSubsidy >>= ($nHeight / 43200);

        return $nSubsidy;
    }
}
