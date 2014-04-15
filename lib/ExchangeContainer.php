<?php

namespace n00bsys0p;

require_once('Exchange.php');

/**
 * A container for multiple exchanges
 *
 * This class allows us to perform actions on all
 * configured exchanges - this facilitates taking
 * averages, and many other potential future features.
 */
class ExchangeContainer
{
    protected $exchanges = array();

    /**
     * Constructor
     *
     * This takes any object that can be treated as an
     * array, and creates an array of local Exchange
     * objects from them.
     *
     * @param array $config An object through which to iterate, and create Exchanges
     */
    public function __construct($config)
    {
        foreach($config as $name => $exchange_ary)
        {
            $exchange_ary['name'] = $name;
            $this->exchanges []= new Exchange($exchange_ary);
        }
    }

    /**
     * Get the average BTC rate for this coin from the
     * configured exchanges.
     */
    public function getAverageBtcRate()
    {
        $total = 0;
        $failed = 0;

        foreach($this->exchanges as $exchange)
        {
            $rate = $exchange->getBtcRate();

            if($rate > 0)
                $total += $exchange->getBtcRate();
            else
                $failed++;
        }

        // Avoid dividing by 0, and ignore any exchanges that
        // return 0 (standard number for Exchange to return on
        // failure to connect or respond).
        return ($total > 0) ? $total / (count($this->exchanges) - $failed) : 0;
    }
}
