<?php

define('APP_DIR', __DIR__);

// Pull in all existing application dependencies
require_once(APP_DIR . '/vendor/autoload.php');
require_once(APP_DIR . '/lib/CalculatorApp.php');
require_once(APP_DIR . '/lib/Config.php');
require_once(APP_DIR . '/lib/adaptors/AbeAdaptor.php');
require_once(APP_DIR . '/lib/adaptors/RpcAdaptor.php');

define('CONFIG_DIR', APP_DIR . '/config');
$config = \n00bsys0p\Config::parse(CONFIG_DIR);

define('CACHE_DIR', APP_DIR . '/' . $config['cache']['dir']);
define('CACHE_EXCHANGE_TIMEOUT', $config['cache']['exchange_timeout']);
define('CACHE_FIAT_TIMEOUT', $config['cache']['fiat_timeout']);
define('CACHE_BLOCK_TIMEOUT', $config['cache']['block_timeout']);

define('GET_PARAM_HASHRATE', 'hr');
define('GET_PARAM_MULTIPLIER', 'hx');
