<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/LitecoinSubsidyFunction.php');

/**
 * Implementation of Vertcoin's subsidy function
 */
class VertcoinSubsidyFunction extends LitecoinSubsidyFunction
{
    /**
     * Vertcoin subsidy function
     *
     * This just shows one way to use the same subsidy
     * function for different coins, yet still maintain
     * extensibility should anything changed.
     *
     * No changes are made to LitecoinSubsidyFunction at
     * all for Vertcoin, so we can just clone the class.
     */
}
