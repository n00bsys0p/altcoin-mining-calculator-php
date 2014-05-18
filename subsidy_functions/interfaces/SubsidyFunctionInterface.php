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
     * to determine the next value, and PHP doesn't hate it
     * if you have extra parameters in implementations of the
     * interface
     */
    public function getBlockValue($nHeight);
}
