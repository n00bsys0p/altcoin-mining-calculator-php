<?php

namespace n00bsys0p;

class Calculator {

    protected $attributes = array();

    protected $coinsPerDay = 0;

    public function __construct($diff, $reward, $btcrate, $btcprice)
    {
        $this->setDifficulty($diff);
        $this->setBlockReward($reward);
        $this->setBitcoinRate($btcrate);
        $this->setBitcoinPrice($btcprice);
    }

    // Main API function. Should be the only one needing to be called
    public function calculatePerDay($hashrate)
    {
        $this->setCoinsPerDay($hashrate);
        $this->setFiatPerDay();

        $cpd = $this->getCoinsPerDay();
        $bpd = $cpd * $this->getBitcoinRate();
        $fpd = $this->getFiatPerDay();

        return array('coins_per_day' => $cpd,
                     'btc_per_day'   => $this->formatAsSatoshi($bpd),
                     'fiat_per_day'  => $fpd);
    }

    protected function cleanRound($number)
    {
        if($number > 100)
            return round($number);
        elseif($number > 10)
            return round($number * 10) / 10;
        elseif($number > 1)
            return round($number * 100) / 100;
        elseif($number > 0.1)
            return round($number * 1000) / 1000;
        elseif($number > 0.01)
            return round($number * 10000) / 10000;
    }

    public static function formatAsSatoshi($sci_rate)
    {
        return sprintf("%.8f", round($sci_rate, 8));
    }

    protected function getCoinsPerDay()
    {
        return $this->coinsPerDay;
    }

    protected function setCoinsPerDay($hashrate)
    {
        // Algorithm ported to PHP from http://www.holynerdvana.com/2014/02/how-to-calculate-coins-per-day-for-any.html
        $seconds_per_day = 24*60*60; // 86400
        $blockreward = $this->getBlockReward();
        $difficulty = $this->getDifficulty();

        $coins_per_day = $seconds_per_day * $blockreward * $hashrate / ($difficulty * (pow(2, 48) / hexdec('0x00000000ffff')));

        $this->coinsPerDay = $this->cleanRound($coins_per_day);
    }

    protected function setBitcoinsPerDay()
    {
        $bpd = $this->getCoinsPerDay() * $this->getBitcoinRate();
        $this->setAttribute('btc_per_day', $bpd);
    }

    protected function getBitcoinsPerDay()
    {
        return $this->getAttribute('btc_per_day');
    }

    protected function setFiatPerDay()
    {
        $currencies = array();
        foreach($this->getBitcoinPrice() as $fiat => $value)
        {
            $fpd = $value * $this->getBitcoinRate() * $this->getCoinsPerDay();
            $currencies[$fiat] = round($fpd, 2);
        }

        $this->setAttribute('fiat_per_day', $currencies);
    }

    protected function getFiatPerDay()
    {
        return $this->getAttribute('fiat_per_day');
    }

    protected function getDifficulty()
    {
        return $this->getAttribute('difficulty');
    }

    protected function setDifficulty($diff)
    {
        $this->setAttribute('difficulty', $diff);
    }

    protected function getBlockReward()
    {
        return $this->getAttribute('block_reward');
    }

    protected function setBlockReward($reward)
    {
        $this->setAttribute('block_reward', $reward);
    }

    protected function getBitcoinRate()
    {
        return $this->getAttribute('bitcoin_rate');
    }

    protected function setBitcoinRate($btcrate)
    {
        $this->setAttribute('bitcoin_rate', $btcrate);
    }

    protected function getBitcoinPrice()
    {
        return $this->getAttribute('bitcoin_price');
    }

    protected function setBitcoinPrice($btcprice)
    {
        $this->setAttribute('bitcoin_price', $btcprice);
    }

    // Internal stuff
    protected function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    protected function getAttribute($attribute)
    {
        return (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : NULL;
    }
}
