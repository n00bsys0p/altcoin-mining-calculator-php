<?php

require 'vendor/autoload.php';
require 'lib/CalculatorApp.php';
require 'lib/Config.php';

define('APP_DIR', getcwd() . '/..');
define('CONFIG_DIR', APP_DIR . '/config');

$config = \n00bsys0p\Config::parse(CONFIG_DIR);

error_log('APPDIR!!!!!!!!!!!!!!! ' . print_r($config, TRUE));

define('CACHE_DIR', APP_DIR . '/' . $config['cache']['dir']);
define('CACHE_EXCHANGE_TIMEOUT', $config['cache']['exchange_timeout']);
define('CACHE_BLOCK_TIMEOUT', $config['cache']['block_timeout']);

define('GET_PARAM_HASHRATE', 'hr');
define('GET_PARAM_MULTIPLIER', 'hx');
