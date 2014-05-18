<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/interfaces/SubsidyFunctionInterface.php');

/**
 * Implementation of GPUCoin's subsidy function
 */
class GpucoinSubsidyFunction implements SubsidyFunctionInterface
{
    /**
     * GPUCoin subsidy function
     *
     * @param  integer $nHeight The block height for which to determine the reward
     * @return integer
     */
    public function getBlockValue($nHeight)
    {
        if($nHeight <= 65535)
            $nSubsidy = 20000;
        elseif($nHeight > 65535 && $nHeight <= 315535)
            $nSubsidy = 10000;
        elseif($nHeight > 315535 && $nHeight <= 565535)
            $nSubsidy = 5000;
        elseif($nHeight > 565535 && $nHeight <= 815535)
            $nSubsidy = 2500;
        elseif($nHeight > 815535 && $nHeight <= 5973024)
            $nSubsidy = 1250;
        else
            $nSubsidy = 0;

        return $nSubsidy;
    }
}
