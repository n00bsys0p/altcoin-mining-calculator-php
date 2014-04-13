<?php

/**
 * A very simple example of how to use this library.
 * This is simply an API - theme the response and implement the
 * interface however you like.
 */

require_once('bootstrap.php');
require_once('lib/HashCalculator.php');
require_once('lib/HirocoinExplorer.php');

// Parse the "mh" get param
if(isset($_GET['mh']) && is_numeric($_GET['mh']) && $_GET['mh'] > 0)
    $megahashes = (float) $_GET['mh'];
else
    die('mh parameter must be integer/float > 0');

// The calculator takes h/s, not Mh/s
$hashes = $megahashes * 1000000;

// We use dependency injection to inject the correct
// explorer because we have to customise the block
// reward subsidy for most different alt coins.
$explorer = '\n00bsys0p\HirocoinExplorer';
$hashcalc = new n00bsys0p\HashCalculator($config, $explorer);

$currencies = array('GBP', 'USD', 'CNY', 'EUR');

$rates = $hashcalc->calculateForHashRate(1, $hashes, $currencies);

$title = 'Hirocoin Mining Calculator';
$body = '<pre>' . json_encode($rates, JSON_PRETTY_PRINT) . '</pre>';

$output = $hashcalc->generateTemplate($title, $body);

echo $output;
