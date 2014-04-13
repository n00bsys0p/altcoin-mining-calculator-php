<?php

require 'vendor/autoload.php';
require 'config/app.php';

define('CACHE_DIR', getcwd() . '/cache');
define('CACHE_EXCHANGE_TIMEOUT', $config['cache']['exchange_timeout']);
define('CACHE_BLOCK_TIMEOUT', $config['cache']['block_timeout']);

