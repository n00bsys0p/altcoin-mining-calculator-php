<?php

namespace n00bsys0p;

require_once('HashCalculator.php');
require_once('ViewBuilder.php');
require_once('ExchangeRateConverter.php');
require_once('CalculatorException.php');

/**
 * Cryptocurrency Hash Calculator Application
 *
 * The container for the entire application. This should
 * make deployment as simple as instantiating the class
 * and calling run.
 */
class CalculatorApp
{
    protected     $config           = NULL;
    protected     $hashRate         = 0;
    protected     $wattage          = 0;
    protected     $perkwh           = 0;
    protected     $kwhfiat          = 'USD';
    protected     $hashCalculators  = array();
    protected     $data             = array();
    protected     $output           = NULL;
    protected     $errors           = array();
    protected     $displayFormat    = 'html';
    protected     $exchangeRates    = array();
    public static $supportedFormats = array('html', 'json');

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

        $format = isset($_GET['fmt']) ? $_GET['fmt'] : $this->displayFormat;
        if(in_array($format, static::$supportedFormats))
            $this->displayFormat = $format;

        // Only instantiate the view builder if it's not an API request
        if($this->displayFormat == 'html')
           $this->viewBuilder = new ViewBuilder();

        $hashCalculator = $this->config['calculator']['classname'];

        // Loop through coins, creating a hash calculator instance for each
        foreach($this->config['app']['coins'] as $coin => $settings)
        {
            $adaptor = array($coin => $this->config['adaptors'][$coin]);
            if(isset($this->config['exchanges'][$coin]))
                $exchanges = $this->config['exchanges'][$coin];
            else
                throw new \Exception('You must configure at least one exchange for ' . $coin);

            $ticker = $this->config['ticker'];
            $this->hashCalculators[$coin] = new $hashCalculator($adaptor, $exchanges, $ticker);
        }
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
        try {
            $this->init();
            $this->requestData();
        } catch(\Exception $e) {
            // Aggregate all errors from all configured calcs
            foreach($this->hashCalculators as $calc)
            {
                $errors = $calc->getErrors();

                if(!empty($errors))
                    $this->errors[$calc->getCoin()] = $calc->getErrors();
            }

            $this->errors['system'] = $e->getMessage();
        }

        /**
         * TODO: Tidy this up to shell out to a new class as
         * this is still a little messy.
         */
        switch($this->displayFormat)
        {
        case 'html':
            $this->prepareView();
            break;
        case 'json':
            $this->prepareJson();
            break;
        }

        $this->displayContent();
    }

    /**
     * Initialise the calculator application
     *
     * This sanity checks then sets all required user-defined variables from
     * input.
     */
    protected function init()
    {
        // Fill out internal data if required
        $multiplier = (isset($_GET[GET_PARAM_MULTIPLIER])) ? $_GET[GET_PARAM_MULTIPLIER] : 1;
        $hashrate = (isset($_GET[GET_PARAM_HASHRATE])) ? $_GET[GET_PARAM_HASHRATE] : $this->hashRate;

        $wattage = (isset($_GET[GET_PARAM_WATTAGE])) ? $_GET[GET_PARAM_WATTAGE] : $this->wattage;
        $perkwh = (isset($_GET[GET_PARAM_PERKWH])) ? $_GET[GET_PARAM_PERKWH] : $this->perkwh;
        $kwhfiat = (isset($_GET[GET_PARAM_COSTCURRENCY])) ? $_GET[GET_PARAM_COSTCURRENCY] : $this->kwhfiat;

        // Make sure the fiat currency the /kwh is supplied in is sane
        if(!preg_match('/^[A-Z]{3}$/', $kwhfiat))
            throw new CalculatorException('Incorrectly formatted fiat cost currency.');

        // If any of these aren't numeric, make them so
        if(!is_numeric($hashrate))   $hashrate = 0;
        if(!is_numeric($multiplier)) $multiplier = 1;
        if(!is_numeric($wattage))    $wattage = 0;
        if(!is_numeric($perkwh))     $perkwh = 0;

        // Set the internal values now we're fairly certain it's all ok
        $this->hashRate = $hashrate * $multiplier;
        $this->wattage = $wattage;
        $this->perkwh = $perkwh;
        $this->kwhfiat = $kwhfiat;
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

        foreach($this->hashCalculators as $calc)
        {
            $coin = $calc->getCoin();
            $data = $calc->calculateForHashRate($this->hashRate, array_keys($currencies));
            $this->data[$coin] = $data;
        }
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
        $data = $this->prepareData();
        $appname = $this->config['app']['appname'];
        $page_vars = array();

        $errors = '';
        if(!empty($data['errors']))
        {
            foreach($data['errors'] as $err)
            {
                $errors .= $this->viewBuilder->prepareError($err);
            }

            $page_vars['ERRORS'] = $errors;
        } else $page_vars['ERRORS'] = NULL;

        $fiat_hdrs = $this->viewBuilder->prepareFiatHeaders($this->config['currencies']);

        $table_rows = '';
        foreach($this->data as $coin => $values)
        {
            $coin_code = $this->config['app']['coins'][$coin]['code'];
            $fiat_values = $this->viewBuilder->prepareFiatValues($values['fiat_per_day']);
            $net_base_values = array();

            foreach($data['coins'][$coin]['daily_return']['fiat'] as $fiat_currency => $detail)
                $net_base_values[$fiat_currency] = $detail['net'];

            $net_values = $this->viewBuilder->prepareFiatValues($net_base_values);
            $table_vars = array(
                'COINCODE' => $coin_code,
                'COINNAME' => $coin,
                'COINSPERDAY' => $values['coins_per_day'],
                'BTCPERDAY' => $values['btc_per_day'],
                'FIATPERDAY' => $fiat_values,
                'NETPERDAY' => $net_values,
                'DIFF' => $data['coins'][$coin]['difficulty'],
            );

            $table_rows .= $this->viewBuilder->prepareTableRow($table_vars);
        }

        $url = '//' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];

        $hr = explode(' ', $data['hashrate']);

        $table_vars = array(
            'HASHRATE' => $hr[0],
            'HASHSUFFIX' => $hr[1],
            'TABLE_ROWS' => $table_rows,
        );

        $table_content = $this->viewBuilder->prepareTable($table_vars);

        $body_vars = array(
            'HASHSUFFIX' => $hr[1],
            'HASHRATE' => explode(' ', $data['hashrate'])[0],
            'WATTAGE' => $this->wattage,
            'PERKWH' => $this->perkwh,
            'CURRENCY' => $this->kwhfiat,
            'CURRENCIES_LIST' => $this->viewBuilder->prepareCurrencyList($this->config['currencies']),
            'TABLES' => $table_content,
            'URL' => $url,
        );

        $body_content = $this->viewBuilder->prepareBody($body_vars);

        $logo = (isset($this->config['app']['logo'])) ?
            $this->viewBuilder->prepareLogo($this->config['app']['logo'], $appname) :
            "";
        $analytics_id = $this->config['analytics']['ua_id'];
        $analytics_url = $this->config['analytics']['ua_url'];

        $page_vars['TITLE'] = $appname;
        $page_vars['LOGO'] = $logo;
        $page_vars['BODY'] = $body_content;
        $page_vars['GA_UAID'] = $analytics_id;
        $page_vars['GA_URL'] = $analytics_url;

        $this->output = $this->viewBuilder->prepareLayout($page_vars);
    }

    /**
     * Return a nicely formatted array of all required data.
     *
     * This currently processes the data to be used in the JSON
     * response. 
     *
     * @return array
     */
    protected function prepareData()
    {
        $multi_ary = array('coins' => array());
        if(!empty($this->errors))
        {
            $response['errors'] = $this->errors;

            return $response;
        }

        $multi_ary['hashrate'] = ($this->hashRate > 0) ?
            Calculator::formatHashRate($this->hashRate) :
            '0 Mh/s';

        $fromCode = $this->kwhfiat;
        $multi_ary['wattage'] = $this->wattage . 'W';
        $multi_ary['perkwh'] = $this->perkwh . $fromCode;

        $currency_converter = new ExchangeRateConverter;

        foreach($this->hashCalculators as $calc)
        {
            $response = array();

            $coin = $calc->getCoin();
            $response['difficulty'] = $calc->getDifficulty();
            $response['daily_return'] = array();
            $response['daily_return']['coins'] = $this->data[$coin]['coins_per_day'];
            $response['daily_return']['btc'] = $this->data[$coin]['btc_per_day'];
            $response['daily_return']['fiat'] = array();

            foreach($this->config['currencies'] as $toCode => $symbol)
            {
                // Totals
                $response['daily_return']['fiat'][$toCode] = array();
                $response['daily_return']['fiat'][$toCode]['symbol'] = $symbol;
                $total = $this->data[$coin]['fiat_per_day'][$toCode];
                $response['daily_return']['fiat'][$toCode]['total'] = $total;

                // Net
                $costPerDay = (($this->wattage / 1000) * $this->perkwh * 24);
                $converted_cost = $currency_converter->convert($fromCode, $toCode, $costPerDay);
                $net = $total - $converted_cost;
                $response['daily_return']['fiat'][$toCode]['cost'] = $converted_cost;
                $response['daily_return']['fiat'][$toCode]['net'] = sprintf('%.02f', $net);
            }

            $multi_ary['coins'][$coin] = $response;
        }

        return $multi_ary;
    }

    protected function prepareJson()
    {
        header('Content-type: application/json');
        $this->output = json_encode($this->prepareData());
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

    protected function addError($error)
    {
        $this->errors []= $error;
    }
}
