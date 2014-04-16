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
     * @param  array $fiat_list An indexed array of currency codes
     * @return array
     */
    public function getFiatRate($fiat_list)
    {
        $list = array();

        if(is_array($fiat_list))
        {
            foreach($fiat_list as $fiat)
            {
                $currency = $this->processFiat($fiat);
                $list[$fiat] = $currency;
            }
        }
        else
        {
            return array($fiat_list => $this->processFiat($fiat_list));
        }

        return $list;
    }

    /**
     * Retrieve the value of a single bitcoin in a given fiat currency
     *
     * Use the configured BTC price ticker to retrieve the value for a
     * single currency. Cached for the same time as exchange data.
     *
     * @param  string $fiat The 3 character fiat currency code for which to find the value
     * @return float
     */
    protected function processFiat($fiat)
    {
        $cache_opts = array('max-age' => CACHE_EXCHANGE_TIMEOUT);
        $cache_filename = 'btcticker_' . strtoupper($fiat);
        $url = $this->config['ticker']['url'];

        // Make sure the fiat code 3 is in the correct format for the API
        $ticker_requires_ucase = $this->config['ticker']['uppercase'];
        $fiat = ($ticker_requires_ucase) ? strtoupper($fiat) : strtolower($fiat);

        // Replace CURR with 
        $url = preg_replace('/\{\{CURR\\}}/', $fiat, $url);
        $response = $this->client->get($url, $cache_filename, $cache_opts, NULL); // We want a NULL response if it fails

        $json = (is_null($response)) ? NULL : json_decode($response);

        return (is_null($json)) ? 0 : $json->last;
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
            $this->calculator = new Calculator(
                $this->getDifficulty(),
                $this->getBlockReward(),
                $this->getBitcoinRate(),
                $this->getFiatRate($fiat)
            );
        } catch(\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $result = $this->calculator->calculatePerDay($hashrate);

        return $result;
    }

    protected function addError($error)
    {
        $this->errors []= $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}

?>
