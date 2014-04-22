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
    protected $coin = NULL;

    /**
     * Constructor
     *
     * This takes any object that can be treated as an
     * array, and creates an array of local Exchange
     * objects from them.
     *
     * @param array $config An object through which to iterate, and create Exchanges
     */
    public function __construct($coin, $config)
    {
        $this->coin = $coin;

        foreach($config as $name => $exchange)
        {
            $exchange['name'] = $name;
            $this->exchanges []= new Exchange($this->coin, $exchange);
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
            try {
                $rate = $exchange->getBtcRate();
            } catch(\Exception $e) {
                $rate = 0;
                $failed++;
                continue;
            }


            if($rate > 0)
                $total += $rate;
            else
                $failed++;
        }

        // Avoid dividing by 0, and ignore any exchanges that
        // return 0 (standard number for Exchange to return on
        // failure to connect or respond).
        return ($total > 0) ? $total / (count($this->exchanges) - $failed) : 0;
    }
}
