<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of Dogecoin's subsidy function
 */
class DogecoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * Dogecoin subsidy function
     *
     * This is implemented as of 21-Apr-2014, so does not
     * include any of the pre-190000 hard forks.
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @param  float   $dDiff   The difficulty of the last block. Not required for Dogecoin
     * @return float
     */
    public function getBlockValue($nHeight, $dDiff)
    {
        $nSubsidy = 500000; // Original block reward

        if($nHeight < 600000)
        {
            $nSubsidy >>= ($nHeight / 100000);
        }
        else
        {
            $nSubsidy = 10000;
        }

        return $nSubsidy;
    }
}
