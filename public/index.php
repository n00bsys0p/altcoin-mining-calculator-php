<?php

require_once('../bootstrap.php');

// Pull in the configured adaptor we want to use in the app.
require_once(APP_DIR . '/subsidy_functions/HirocoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/DarkcoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/LimecoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/QuebecoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/LogicoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/MunitiSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/GlobalDenominationSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/HighFiveCoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/GivecoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/HashcoinSubsidyFunction.php');

$app = new \n00bsys0p\CalculatorApp($config);

try {
    $app->run();
} catch(\Exception $e) {
    die($e->getMessage());
}
