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
$mh_isset = (isset($_GET['mh']) && is_numeric($_GET['mh']) && $_GET['mh'] > 0);

$megahashes = $mh_isset ? (float) $_GET['mh'] : 0;

// The calculator takes h/s, not Mh/s
$hashes = $megahashes * 1000000;

/**
 * We use dependency injection to inject the correct
 * explorer because we have to customise the block
 * reward subsidy for most different alt coins.
 */
$explorer = '\n00bsys0p\HirocoinExplorer';
$hashcalc = new n00bsys0p\HashCalculator($config, $explorer);

$currencies = array('GBP' => '£', 'USD' => '$', 'CNY' => '¥', 'EUR' => '€');

$rates = $hashcalc->calculateForHashRate(1, $hashes, array_keys($currencies));

/**
 * View stuff
 */

$fiat_vars = $hashcalc->generateTemplate('fiat', $rates['fiat_per_day']);

$body_vars = array(
    'MEGAHASHES' => $megahashes,
    'COINSPERDAY' => $rates['coins_per_day'],
    'BTCPERDAY' => $rates['btc_per_day'],
    'FIATPERDAY' => $fiat_vars
);

$body_tpl = $hashcalc->generateTemplate('body', $body_vars);

$page_vars = array(
    'TITLE' => 'Hirocoin Mining Calculator',
    'BODY' => $body_tpl,
);

$main_tpl = $hashcalc->generateTemplate('main', $page_vars);

echo $main_tpl;
