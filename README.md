# Cryptocurrency calculator for altcoins

## Built-in currencies:
### X11:
 * Hirocoin
 * Darkcoin
 * Limecoin
### Scrypt:
 * Litecoin
 * Dogecoin
### Scrypt-n:
 * Vertcoin
 * Execoin
 * GPUCoin

Only the X11 coins have pre-configured adaptors and exchanges, but that should serve to be a sufficient example. The RPC and Abe interfaces for all coins are identical for all required features.

You cannot set up multiple algorithms on the same instance of this application, as the hash rates won't match. To get around this, you can run multiple instances of the application, each configured for coins of a specific algorithm as the app has a built-in JSON API.

## Introduction

This is a PHP app for calculating profitability of Hirocoin, with the facility to extend it with ease to support almost any other altcoin.

It has Hirocoin built in, but to convert for another altcoin it ought to be a simple enough process. To do so, simply subclass either `\n00bsys0p\AbeAdaptor` or `\n00bsys0p\RpcAdaptor`, and implement the abstract function getBlockValue($nHeight) for that coin. This code is simply the reward subsidy function from the coin, ported to PHP. For example, Hirocoin's is 400 >>= ($nHeight / 840000), where $nHeight is the given block height.

The configuration for the application are all .yml files in the /config directory. They are supplied with the .example suffix so you have to go through each of them on install of the application and fill out required details. The example files are all usable for Hirocoin.

The current edition uses an [ABE](https://github.com/bitcoin-abe/bitcoin-abe) data source, or can speak to the coin server daemon directly via JSON RPC if the server you're running the app on is a full node for your chosen cryptocurrency.

It has caching built in, in order to avoid either flooding the local daemon, or causing too many time-consuming outbound connections. It will cache block heights for a preconfigured time ($config['cache']['block_timeout'] in config/cache.yml), so configure this to be the estimated number of seconds between blocks for your application. It will also cache exchange data (including fiat), for a period of time. This is done by configuring $config['cache']['exchange_timeout'] in the same file.

I have deliberately left it at a daily calculator, because many coins are now using as low as 1 block difficulty retargeting, so trying to predict profitability for a month, or even a week can be quite misleading.

Credit for the algorithm used to calculate the coin rewards goes to Jarred Walton (https://plus.google.com/+JarredWalton). Credit for libaries used go to their various authors.

## Installation

To install the application, you require a web server that supports PHP version 5.3.x. Simply make sure that the folder is owned by the web server's user and point a virtual host's document root at the /public folder, serving index.php. If you install the application in /var/www, the virtualhost configuration file may look something like this:

```
<VirtualHost *:80>
    ServerAdmin you@yourdomain.org
    #ServerName yourdomain.org
    DocumentRoot /var/www/public

    <Directory /var/www/public>
        DirectoryIndex index.php index.php
        AllowOverride None
        Order allow,deny
        allow from all
    </Directory>
</VirtualHost>
```

This will set your virtualhost to listen on all addresses on port 80, and serve the index.php file from the /public folder. Change ServerAdmin to your email address and if you're running this on a specific domain, uncomment and modify that line too.

The dependencies for this project can be pulled in very easily using [composer](https://getcomposer.org/). Once you have composer installed, you need to run `./composer.phar install` in the folder into which you installed the application.

## Adding new coins
All you really need to add a new coin is the block reward subsidy function.

Coins you wish your app to use are in config/app.yml. Set a new element in the coins array, titled the name of the coin, and containing a single attribute, `code` which is the coin's short code. You can add any coin which is configured in config/adaptors.yml and config/exchanges.yml.

To implement the subsidy function and configure the adaptor, first create a class in the subsidy_functions folder that implements \n00bsys0p\SubsidyFunctionInterface, found in subsidy_functions/interfaces. This shows that you need to build a function that implements the reward subsidy function for any given coin. Some coins use the previous block's difficulty (nBits) to work out the subsidy, so that is included. Supply $nBits in integer format, not hex. If you do not require it, implement the function with a default vaue for $nBits, such as `($nHeight, $nBits = NULL)` or similar.

Next, you need to configure the coin's adaptor settings in config/adaptors.yml. Check the example configuration to see your options. You can even use your own adaptors by specifying the fully namespaced class name as the `type`. Make sure you require or require_once the file that contains your subsidy_function class at some point before instantiating the application.

The coin's logo should be in PNG format, and located in public/assets/img/coin_CODE.png, where CODE is the code configured in config/app.yml

## Theming

Only briefly going into this.. All used templates and partials are in the /tpl folder. The format used for replacing content is to search for keys all in capitals surrounded by `{{}}`, such as `{{TITLE}}`. It's only simple, but it makes it very simple to theme to your requirements. The logo file is stored in /public/assets/img/logo.png. Simply replace this file to use your own.

## License

This work is released under the MIT license, except for the dependencies, which are the work of their original authors and released under whichever license is stated within their source code.
