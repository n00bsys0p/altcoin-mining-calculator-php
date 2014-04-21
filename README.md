# Cryptocurrency calculator for altcoins

This has been entirely in community spirit, so please consider donating if you find this of use! I can see that this application could be used at the core of many varieties of multipool, as it's extremely lightweight, and can theoretically support any coin. Even if a coin's subsidy function was too complex for PHP to handle (fairly unlikely), a PHP module could be made to achieve this in C with minimal effort.

#### DONATION ADDRESSES:

 * BTC: 1Cca2UgfPZdieW3Fqg6B4ZLj5y3odj1HMQ

 * HIRO: HFTmSXTzyZhWKN8JPiP1Lu82sx7QpGESZc

 * DRK: Xs8g1qfEVzKf4Tt6hY9k7z2Sh6EX3d7vRT

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

This is a PHP app for calculating profitability of any given cryptocurrency that you can access via either JSON RPC or have an external Abe-based block explorer.

The current edition uses an [ABE](https://github.com/bitcoin-abe/bitcoin-abe) data source, or can speak to the coin server daemon directly via JSON RPC if the server you're running the app on is a full node for your chosen cryptocurrency.

## Configuration

The configuration files for the application are all .yml files in the /config directory. They are supplied with the .example suffix so you have to go through each of them on install of the application and fill out required details. The example files are all usable for Hirocoin, Darkcoin and Limecoin with just RPC connection information to configure. Just remove the .example suffix.

It has caching built in, in order to avoid either flooding the local daemon, or causing too many time-consuming outbound connections. It will cache block heights for a preconfigured time ($config['cache']['block_timeout'] in config/cache.yml), so configure this to be the estimated number of seconds between blocks for your application. It will also cache exchange data (including fiat), for a period of time. This is done by configuring $config['cache']['exchange_timeout'] in the same file.

I have deliberately left it at a daily calculator, because many coins are now using as low as 1 block difficulty retargeting, so trying to predict profitability for a month, or even a week can be quite misleading.

Credit for the algorithm used to calculate the coin rewards goes to Jarred Walton (https://plus.google.com/+JarredWalton). Credit for libaries used go to their various authors.

## Installation

The dependencies for this project can be pulled in very easily using [composer](https://getcomposer.org/). Once you have composer installed, you need to run `./composer.phar install` in the folder into which you installed the application.

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

An Apache implementation on a multipool may look as follows:
```
<VirtualHost 127.0.0.1:8001>
    DocumentRoot /var/www/scrypt/public

    <Directory /var/www/scrypt/public>
        DirectoryIndex index.php index.php
        AllowOverride None
        Order allow,deny
        allow from all
    </Directory>
</VirtualHost>

<VirtualHost 127.0.0.1:8002>
    DocumentRoot /var/www/x11/public

    <Directory /var/www/x11/public>
        DirectoryIndex index.php index.php
        AllowOverride None
        Order allow,deny
        allow from all
    </Directory>
</VirtualHost>

<VirtualHost 127.0.0.1:8003>
      DocumentRoot /var/www/scrypt-n/public
   
      <Directory /var/www/scrypt-n/public>
          DirectoryIndex index.php index.php
          AllowOverride None
          Order allow,deny
          allow from all
      </Directory>
  </VirtualHost>
```

This would serve 3 separate instances of the application, installed in /var/www/{scrypt,scrypt-n,x11}. Each of these would be configured to support coins using that particular algorithm. They will also only listen on the loopback interface, avoiding exposing these services to the outside world.

## Adding new coins
All you really need to add a new coin is the block reward subsidy function.

Coins you wish your app to use are set in config/app.yml. Set a new element in the coins array, titled the name of the coin, and containing a single attribute, `code` which is the coin's short code. You can add any coin which is configured in config/adaptors.yml and config/exchanges.yml.

It has a number of coins built in, but to convert for another altcoin it is a simple enough process:

To do so, simply create a class that implements the interface `\n00bsys0p\SubsidyFunctionInterface` found in subsidy_functions/interfaces/SubsidyFunctionInterface.php , and implement its only required function `getBlockValue($nHeight, $dDiff)` for that coin.

This code is simply the reward subsidy function from the coin, ported to PHP. For example, Hirocoin's is 400 >>= ($nHeight / 840000), where $nHeight is the given block height. Darkcoin, amongst others, also uses the difficulty of the previous block to work out the next value, so the system will supply the latest difficulty, even if it is not required.
  
You will then need to set up exchanges and adaptors for your new coins. Just follow the simple setups in config/{adaptors,exchanges}.yml and finally add the coins, along with their codes to config/app.yml to load them at run time.

Next, you need to configure the coin's adaptor settings in config/adaptors.yml. Check the example configuration to see your options. You can even use your own adaptors by specifying the fully namespaced class name as the `type`. Make sure you require or require_once the file that contains your subsidy_function class at some point before instantiating the application. The simplest place to do this is in public/index.php.

The coin's logo for the visual web interface should be in PNG format, and located in public/assets/img/coin_CODE.png, where CODE is the code configured in config/app.yml. Resolution not that important because it gets resized really small for display.

## Theming

Only briefly going into this.. All used templates and partials are in the /tpl folder. The format used for replacing content is to search for keys all in capitals surrounded by `{{}}`, such as `{{TITLE}}`. It's only simple, but it makes it relatively simple to theme to your requirements.

## License

This work is released under the MIT license, except for the dependencies, which are the work of their original authors and released under whichever license is stated within their source code.
