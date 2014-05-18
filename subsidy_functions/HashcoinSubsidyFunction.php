<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Hashcoin subsidy function
 *
 * This provides the value of a block based on the height and
 * difficulty of the previous block.
 */
class HashcoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Hashcoin's subsidy function ported from GetBlockValue
     *
     * This does require dDiff
     *
     * @param  integer $nHeight The block height for which to find the subsidy
     * @param  integer $dDiff   The given block's difficulty - we set this to 0 by default as the interface does not contain any other parameters
     * @return float
     */
    public function getBlockValue($nHeight, $dDiff = 0)
    {
        $nSubsidy = 0;
        if($nHeight >= 5465) {
            if(($nHeight >= 17000 && $dDiff > 75) || $nHeight >= 24000) { // GPU/ASIC difficulty calc
                // 2222222/(((x+2600)/9)^2)
                $nSubsidy = (2222222.0 / (pow(($dDiff+2600.0)/9.0,2.0)));
                if ($nSubsidy > 25) $nSubsidy = 25;
                if ($nSubsidy < 5) $nSubsidy = 5;
            } else { // CPU mining calc
                $nSubsidy = (11111.0 / (pow(($dDiff+51.0)/6.0,2.0)));
                if ($nSubsidy > 500) $nSubsidy = 500;
                if ($nSubsidy < 25) $nSubsidy = 25;
            }
        } else {
            $nSubsidy = (1111.0 / (pow(($dDiff+1.0),2.0)));
            if ($nSubsidy > 500) $nSubsidy = 500;
            if ($nSubsidy < 1) $nSubsidy = 1;
        }

        // yearly decline of production by 7% per year
        for($i = 210240; $i <= $nHeight; $i += 210240) $nSubsidy *= 0.93;

        return $nSubsidy;
    }
}
