<?php

namespace n00bsys0p;

require_once('Exchange.php');

class ExchangeContainer
{
    protected $exchanges = array();

    public function __construct($config)
    {
        foreach($config as $name => $exchange_ary)
        {
            $exchange_ary['name'] = $name;
            $this->exchanges []= new Exchange($exchange_ary);
        }
    }

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
