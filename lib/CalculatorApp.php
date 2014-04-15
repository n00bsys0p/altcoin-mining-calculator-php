<?php

namespace n00bsys0p;

require_once('HashCalculator.php');
require_once('ViewBuilder.php');

/**
 * Cryptocurrency Hash Calculator Application
 *
 * The container for the entire application. This should
 * make deployment as simple as instantiating the class
 * and calling run.
 */
class CalculatorApp
{
    protected $config         = NULL;
    protected $hashRate       = NULL;
    protected $hashCalculator = NULL;
    protected $data           = array();
    protected $output         = NULL;

    /**
     * Constructor
     *
     * Set up the application configuration based on the
     * configuration array passed.
     *
     * @param array $config The application configuration
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->viewBuilder = new ViewBuilder;

        /**
         * We use dependency injection to inject the correct
         * explorer because we have to customise the block
         * reward subsidy for most different alt coins. You
         * can also choose to inject your own calculator.
         */
        $hashCalculator = $this->config['calculator']['classname'];
        $explorer = $this->config['explorer']['classname'];
        $this->hashCalculator = new $hashCalculator($this->config, $explorer);
    }

    /**
     * Run the application from start to finish
     *
     * Nothing more to say about this - it runs all the steps the app
     * requires to run from start to finish.
     */
    public function run()
    {
        // These must be run in this order
        $this->init();
        $this->requestData();
        $this->prepareView();
        $this->displayContent();
    }

    /**
     * Initialise the calculator application
     *
     * This sanity checks and sets sane defaults for the
     * user-defined variables.
     */
    protected function init()
    {
        // Sanity checks
        $hashrate = isset($_GET[GET_PARAM_HASHRATE]) ? $_GET[GET_PARAM_HASHRATE] : 0;
        $multiplier = isset($_GET[GET_PARAM_MULTIPLIER]) ? $_GET[GET_PARAM_MULTIPLIER] : 1;

        if(!is_numeric($hashrate))
            $hashrate = 0;
        if(!is_numeric($multiplier))
            $multiplier = 1;

        $this->hashRate = $hashrate *= $multiplier;
    }

    /**
     * Retrieve the required data
     *
     * Passes the configured currencies to the calculator to get daily
     * earnings data on each. This must be run after the application has
     * been initialised to prepare the data for formatting.
     */
    protected function requestData()
    {
        $currencies = $this->config['currencies'];

        $this->data = $this->hashCalculator->calculateForHashRate($this->hashRate, array_keys($currencies));
    }

    /**
     * Prepare the displayed view
     *
     * This puts together all the currently prepared information into a browser
     * compatible display format. It uses the local ViewBuilder instance to
     * do this. This must be run after requestData, as before that there is no
     * data to prepare.
     */
    protected function prepareView()
    {
        /**
         * Loop through the fiat variables, generating the required HTML
         * to insert into the table - both for headings and values
         */
        $fiat_hdr = $this->viewBuilder->prepareFiatHeaders($this->config['currencies']);
        $fiat_val = $this->viewBuilder->prepareFiatValues($this->data['fiat_per_day']);

        $hashrate_fmt = Calculator::formatHashRate($this->hashRate);

        /**
         * Prepare the main (non-fiat-specific) page content.
         */
        $body_vars = array(
            'HASHRATE' => explode(' ', $hashrate_fmt)[0],
            'HASHSUFFIX' => explode(' ',$hashrate_fmt)[1],
            'COINSPERDAY' => $this->data['coins_per_day'],
            'BTCPERDAY' => $this->data['btc_per_day'],
            'FIATPERDAY' => $fiat_val,
        );

        $body = $this->viewBuilder->prepareBody($body_vars);

        /**
         * Prepare the layout
         */
        $page_vars = array(
            'TITLE' => 'Hirocoin Mining Calculator',
            'BODY' => $body,
        );

        $this->output = $this->viewBuilder->prepareLayout($page_vars);
    }

    /**
     * Display the content to the user.
     *
     * This function could be redefined to set custom headers in the
     * case of the application being run as an API rather than embedded
     * in a web page.
     */
    protected function displayContent()
    {
        echo $this->output;
    }
}
