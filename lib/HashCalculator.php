<?php

namespace n00bsys0p;

require_once('ExchangeContainer.php');
require_once('Calculator.php');
require_once('CachingHttpClient.php');
require_once('TemplateProcessor.php');

/**
 * Hash Calculator
 *
 * This is the main calculator controller. It coordinates all
 * the actions between data models and the main application.
 */
class HashCalculator {

    // App configuration
    protected $config = NULL;

    // Adaptor object to handle crypto network queries
    protected $adaptor = NULL;

    // ExchangeContainer for performing operations
    // on grouped Exchange objects
    protected $exchanges = NULL;

    // An HTTP client
    protected $client = NULL;

    // The actual calculator
    protected $calculator = NULL;

    // Basic message bag for errors
    protected $errors = array();

    /**
     * Constructor
     *
     * Configures the controller object
     *
     * @param array  $config   The application configuration
     * @param string $adaptor The class name of the adaptor to use
     */
    public function __construct($config, $adaptor)
    {
        $this->config    = $config;
        $this->adaptor  = new $adaptor($config['adaptor']);
        $this->exchanges = new ExchangeContainer($config['exchanges']);
        $this->client    = new CachingHttpClient;
    }

    /**
     * Get current block reward
     *
     * Query the adaptor for the current block reward
     *
     * @return integer
     */
    public function getBlockReward()
    {
        return $this->adaptor->getBlockReward();
    }

    /**
     * Get current block difficulty
     *
     * Query the configured adaptor for the current block difficulty
     *
     * @return float
     */
    public function getDifficulty()
    {
        return $this->adaptor->getDifficulty();
    }

    /**
     * Return a list of fiat rates
     *
     * Get fiat rates for the altcoin for a single
     * or list of fiat currencies as provided
     *
     * Use the configured BTC price ticker to retrieve the value for a
     * single currency. Cached for the same time as exchange data.
     *
     * @param  array $fiat_list An indexed array of currency codes
     * @return array
     */
    public function getFiatRate($fiat_list)
    {
        $cache_opts = array('max-age' => CACHE_EXCHANGE_TIMEOUT);
        $cache_filename = 'btcticker';
        $url = $this->config['ticker']['url'];

        // Replace CURR with fiat name
        $response = $this->client->get($url, $cache_filename, $cache_opts);

        $values = $this->processFiat($fiat_list, json_decode($response));

        return $values;
    }

    /**
     * Retrieve the value of a single bitcoin in a given or range of
     * fiat currencies.
     *
     * Parses $json with the configured locating string from the config
     * file for each currency code supplied.
     *
     * @param  array  $fiat_list An array of currency short codes
     * @param  object $json      A PHP native JSON object
     * @return array
     */
    protected function processFiat($fiat_list, $json)
    {
        $json_srch = $this->config['ticker']['json_string'];

        $list = array();
        foreach($fiat_list as $fiat)
        {
            // Make sure the fiat short code is in the correct format for the API
            $ticker_ucase = $this->config['ticker']['uppercase'];
            $fiat = ($ticker_ucase) ? strtoupper($fiat) : strtolower($fiat);

            // Parse the data from this currency's element
            $value = $this->processJson($json->$fiat, $json_srch);
            $list[$fiat] = $value;
        }

        return $list;
    }

    /**
     * Get the average current bitcoin per coin rate
     *
     * Averages the bitcoin price over all configured exchange APIs
     * and converts it from PHP native (sci) format into a readable
     * value of Satoshis (1e-08 to 0.00000001)
     *
     * @return float
     */
    public function getBitcoinRate()
    {
        $rate = $this->exchanges->getAverageBtcRate();
        // Convert to satoshis from native PHP scientific notation
        return Calculator::formatAsSatoshi($rate);
    }

    /**
     * Calculate daily income for a given hashrate
     *
     * This aggregates all features into one function, which allows
     * the user to trivially retrieve the projected income in
     * cryptocurrency and fiat values by just supplying raw
     * hash/second and an optional list of currencies (default USD)
     *
     * @param integer $hashrate The hash/second for which to calculate projected income
     * @param array   $fiat     Optional indexed array of fiat currency codes
     */
    public function calculateForHashRate($hashrate, $fiat = 'USD')
    {
        try {
            $diff = $this->getDifficulty();
            $rewd = $this->getBlockReward();
            $btcr = $this->getBitcoinRate();
            $ftrt = $this->getFiatRate($fiat);
            $this->calculator = new Calculator($diff, $rewd, $btcr, $ftrt);
        } catch(\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $result = $this->calculator->calculatePerDay($hashrate);

        return $result;
    }

    /**
     * Return the local error bag
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error to the local error bag
     */
    protected function addError($error)
    {
        $this->errors []= $error;
    }

    /**
     * Retrieve a given value from a JSON object
     *
     * Use the . delimited $json_srch to parse the relevant nested
     * elements out of the supplied $json object/array
     *
     * @return mixed
     */
    protected function processJson($json, $json_srch)
    {
        foreach(explode('.', $json_srch) as $element)
        {
            $json = is_array($json) ? $json[$element] : $json->$element;
        }

        return $json;
    }
}

?>
