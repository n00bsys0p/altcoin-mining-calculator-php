# Cryptocurrency calculator for altcoins (Hirocoin built in)

This is a PHP app for calculating profitability of Hirocoin, with the facility to extend it with ease to support almost any other altcoin.

Very basic setup so far. It has Hirocoin built in, but to convert for another altcoin it ought to be a simple enough process, as you'd just need to create one class to define the block reward subsidy function for the coin, and change the configuration to describe the exchanges and coin specs.

It has caching built in. It will cache block heights for a preconfigured time ($config['cache']['block_timeout'] in config/app.php), so configure this to be the estimated number of seconds between blocks for your application. It will also cache exchange data (including fiat), for a period of time. This is done by configuring $config['cache']['exchange_timeout'] in the same file.

There is an example configuration file in config/ called app.php.example, which shows a sample configuration set up for Hirocoin.

The current edition uses an [ABE](https://github.com/bitcoin-abe/bitcoin-abe) based explorer, but I intend to build in a feature which grabs all this information directly from a local instance of any altcoin daemon that supports the required features. I have deliberately left it at a daily calculator, because many coins are now using 1 block difficulty retargeting.

Credit for the algorithm used to calculate the coin rewards goes to Jarred Walton (https://plus.google.com/+JarredWalton).

## Examples

Check out hirocoin_example.php file for a very rudimentary example - just set the GET parameter "mh" to a numeric value (int or float greater than 0), and this will then work out the number of coins you will earn per day, check that against a non-weighted average across all configured exchanges, then compare to value against BTC, and even work out the fiat values using [Bitcoin Average](https://bitcoinaverage.com/)'s API. You can change the site that reports the bitcoin price by changing the values in config/app.php.

## License

This work is released under the MIT license, except for the dependencies, which are the work of their
original authors and released under whichever license is stated.
