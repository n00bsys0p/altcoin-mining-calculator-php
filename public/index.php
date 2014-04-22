<?php

require_once('../bootstrap.php');

// Pull in the configured adaptor we want to use in the app.
require_once(APP_DIR . '/subsidy_functions/HirocoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/DarkcoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/LimecoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/QuebecoinSubsidyFunction.php');
require_once(APP_DIR . '/subsidy_functions/LogicoinSubsidyFunction.php');

$app = new \n00bsys0p\CalculatorApp($config);

try {
    $app->run();
} catch(\Exception $e) {
    die($e->getMessage());
}
