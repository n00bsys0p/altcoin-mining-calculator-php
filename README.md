# Cryptocurrency calculator for altcoins (Hirocoin built in)

This is a PHP app for calculating profitability of Hirocoin, with the facility to extend it with ease to support almost any other altcoin.

It has Hirocoin built in, but to convert for another altcoin it ought to be a simple enough process. To do so, simply subclass either n00bsys0p\AbeAdaptor or n00bsys0p\RpcAdaptor, and implement the abstract function getBlockValue($nHeight) for that coin. This code is simply the reward subsidy function from the coin, ported to PHP. For example, Hirocoin's is 400 >>= ($nHeight / 840000), where $nHeight is the given block height.

The configuration for the application are all .yml files in the /config directory. They are supplied with the .example suffix so you have to go through each of them on install of the application and fill out required details. The example files are all usable for Hirocoin.

The current edition uses an [ABE](https://github.com/bitcoin-abe/bitcoin-abe) data source, or can speak to the coin server daemon directly via JSON RPC if the server you're running the app on is a full node for your chosen cryptocurrency.

It has caching built in, in order to avoid either flooding the local daemon, or causing too many time-consuming outbound connections. It will cache block heights for a preconfigured time ($config['cache']['block_timeout'] in config/cache.yml), so configure this to be the estimated number of seconds between blocks for your application. It will also cache exchange data (including fiat), for a period of time. This is done by configuring $config['cache']['exchange_timeout'] in the same file.

I have deliberately left it at a daily calculator, because many coins are now using 1 block difficulty retargeting, so trying to predict profitability for a month, or even a week can be quite misleading.

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

## License

This work is released under the MIT license, except for the dependencies, which are the work of their original authors and released under whichever license is stated within their source code.
