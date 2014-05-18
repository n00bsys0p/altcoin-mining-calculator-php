<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Darkcoin subsidy function
 *
 * This provides the value of a block based on the height and
 * difficulty of the previous block.
 */
class DarkcoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Darkcoin's subsidy function ported from GetBlockValue
     *
     * Note: This function is only set to support the algorithm
     * as of the time of writing. If this changes, this function will
     * need to be rewritten.
     *
     * Written 21-Apr-2014, not guaranteed to work if there's a hard fork
     * relating to the subsidy after this date.
     *
     * @param  integer $nHeight The block height for which to find the subsidy
     * @param  integer $dDiff   The given block's difficulty - we set this to 0 by default as the interface does not contain any other parameters
     * @return float
     */
    public function getBlockValue($nHeight, $dDiff = 0)
    {
        $nSubsidy = 0;

        // 2222222/(((x+2600)/9)^2)
        $nSubsidy = (2222222.0 / (pow(($dDiff+2600.0)/9.0,2.0)));
        if ($nSubsidy > 25) $nSubsidy = 25;
        if ($nSubsidy < 5) $nSubsidy = 5;

        // yearly decline of production by 7% per year, projected 21.3M coins max by year 2050.
        for($i = 210240; $i <= $nHeight; $i += 210240) $nSubsidy *= 0.93;

        return $nSubsidy;
    }
}
