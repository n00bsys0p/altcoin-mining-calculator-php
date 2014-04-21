<?php

namespace n00bsys0p;

require_once(APP_DIR . '/subsidy_functions/LitecoinSubsidyFunction.php');

/**
 * Implementation of Execoin's subsidy function
 */
class ExecoinSubsidyFunction extends LitecoinSubsidyFunction
{
    /**
     * Execoin subsidy function
     *
     * This just shows one way to use the same subsidy
     * function for different coins, yet still maintain
     * extensibility should anything change.
     *
     * No changes are made to LitecoinSubsidyFunction at
     * all for Execoin, so we can just clone the class.
     */
}
