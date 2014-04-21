<?php

namespace n00bsys0p;

/**
 * Interface for all subsidy functions
 *
 * To add a new coin, you need to make a class that implements
 * this interface, which is used to fine the reward subsidy
 * for that coin. PHP is very similar to C in many aspects,
 * so this process is often trivial.
 */
interface SubsidyFunctionInterface
{
    /**
     * Some block rewards use the diff of the previous block
     * to determine the next value, so you must supply it, even
     * if your coin does not require it. If you don't use it, just
     * enter NULL or similar
     */
    public function getBlockValue($nHeight, $dDiff);
}
