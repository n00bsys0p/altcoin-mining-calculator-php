<?php

namespace n00bsys0p;

/**
 * The actual calculator
 *
 * Does the actual calculation based on passed values,
 * and responds with a formatted array. It also provides
 * static functions for formatting hash rates and crypto
 * currency fractions in readable ways.
 */
class Calculator {

    protected $attributes = array();

    protected $coinsPerDay = 0;

    /**
     * Constructor
     *
     * Configure the internal values on which to
     * perform the block reward calculation.
     */
    public function __construct($diff, $reward, $btcrate, $btcprice)
    {
        $this->setDifficulty($diff);
        $this->setBlockReward($reward);
        $this->setBitcoinRate($btcrate);
        $this->setBitcoinPrice($btcprice);
    }

    /**
     * Perform the actual calculation
     *
     * @param integer $hashrate The raw hash/second rate on which to perform the calculations
     */
    public function calculatePerDay($hashrate)
    {
        // Calculate
        $this->setCoinsPerDay($hashrate);
        $this->setFiatPerDay();

        $cpd = $this->getCoinsPerDay();
        $bpd = $cpd * $this->getBitcoinRate();
        $fpd = $this->getFiatPerDay();

        // Format & respond
        return array('coins_per_day' => (int) $cpd,
                     'btc_per_day'   => $this->formatAsSatoshi($bpd),
                     'fiat_per_day'  => $fpd);
    }

    /**
     * Format a raw hash/second rate
     *
     * This will take a raw hash rate (e.g. 11500000)
     * and turn it into a human readable string
     * (e.g. "11.5 Mh/s").
     *
     * @param integer $hashrate The raw h/s to format as human readable
     * @return string
     */
    public static function formatHashRate($hashrate)
    {
        if($hashrate > 1000000000000)
        {
            $hashrate /= 1000000000000;
            $suffix = 'P';
        }
        elseif($hashrate > 1000000000)
        {
            $hashrate /= 1000000000;
            $suffix = 'T';
        }
        elseif($hashrate > 1000000)
        {
            $hashrate /= 1000000;
            $suffix = 'M';
        }
        elseif($hashrate > 1000)
        {
            $hashrate /= 1000;
            $suffix = 'K';
        }
        elseif($hashrate > 0)
            $suffix = '';

        return $hashrate . ' ' . $suffix . 'h/s';
    }

    /**
     * Ported directly from the original source.
     *
     * Cleans a number by rounding it.
     *
     * @return integer
     */
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

    /**
     * Formats any number in Satoshis
     *
     * This will format a number in any PHP native format
     * into a Satoshi-like format (8 decimal places, no
     * scientific notation. e.g., 1e-08 to 0.00000001)
     *
     * @return string
     */
    public static function formatAsSatoshi($native_numerical)
    {
        return sprintf("%.8f", round($native_numerical, 8));
    }

    /**
     * Get the coins per day
     *
     * This just returns the internal value coinsPerDay, set by
     * the function setCoinsPerDay($hashrate).
     *
     * @return integer
     */
    protected function getCoinsPerDay()
    {
        return $this->coinsPerDay;
    }

    /**
     * Set the coins per day for a given hash rate
     *
     * This does the actual calculation. The algorithm is ported
     * from the JS calculator on the Holy Nerdvana blog post by
     * Jarred Walton.
     *
     * @param integer $hashrate The raw hash rate for which to find the coins per day
     */
    protected function setCoinsPerDay($hashrate)
    {
        // Algorithm ported to PHP from http://www.holynerdvana.com/2014/02/how-to-calculate-coins-per-day-for-any.html
        $seconds_per_day = 24*60*60; // 86400
        $blockreward = $this->getBlockReward();
        $difficulty = $this->getDifficulty();

        $coins_per_day = ($seconds_per_day * $blockreward * $hashrate) / ($difficulty * (pow(2, 48) / hexdec('0x00000000ffff')));

        // Set the local variable to a rounded up integer of the figure
        $this->coinsPerDay = $this->cleanRound($coins_per_day);
    }

    /**
     * Set the local bitcoins per day attribute
     *
     * Multiply the bitcoin rate for this coin by the coins per day
     * to return the number of bitcoins earned in one day.
     */
    protected function setBitcoinsPerDay()
    {
        $bpd = $this->getCoinsPerDay() * $this->getBitcoinRate();
        $this->setAttribute('btc_per_day', $bpd);
    }

    /**
     * Get local bitcoins per day attribute
     *
     * @return string
     */
    protected function getBitcoinsPerDay()
    {
        return $this->getAttribute('btc_per_day');
    }

    /**
     * Set the local fiat per day array attribute
     *
     * Work out the fiat per day figure for each configured
     * fiat currency.
     */
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

    /**
     * Return the local fiat per day attribute
     *
     * @return array
     */
    protected function getFiatPerDay()
    {
        return $this->getAttribute('fiat_per_day');
    }

    /**
     * Return the local difficulty attribute
     *
     * return float
     */
    protected function getDifficulty()
    {
        return $this->getAttribute('difficulty');
    }

    /**
     * Set the local difficulty attribute
     *
     * @param float $diff The value to which to set the local difficulty attribute
     */
    protected function setDifficulty($diff)
    {
        $this->setAttribute('difficulty', $diff);
    }

    /**
     * Return the local block reward attribute
     *
     * @return integer
     */
    protected function getBlockReward()
    {
        return $this->getAttribute('block_reward');
    }

    /**
     * Set the local block reward attribute
     *
     * @param integer $reward The block reward to store locally
     */
    protected function setBlockReward($reward)
    {
        $this->setAttribute('block_reward', $reward);
    }

    /**
     * Return the local bitcoin per coin rate
     *
     * @return float
     */
    protected function getBitcoinRate()
    {
        return $this->getAttribute('bitcoin_rate');
    }

    /**
     * Set the local bitcoin rate attribute
     *
     * @param float $btcrate The current bitcoins per coin rate for this coin.
     */
    protected function setBitcoinRate($btcrate)
    {
        $this->setAttribute('bitcoin_rate', $btcrate);
    }

    /**
     * Return the local bitcoin price array
     *
     * @return array
     */
    protected function getBitcoinPrice()
    {
        return $this->getAttribute('bitcoin_price');
    }

    /**
     * Set the local bitcoin price attribute
     *
     * @param array $btcprice An associative array of currency codes and bitcoin prices for each`
     */
    protected function setBitcoinPrice($btcprice)
    {
        $this->setAttribute('bitcoin_price', $btcprice);
    }

    /**
     * Set a local attribute
     *
     * @param string $attribute The key of the attribute to set
     * @param mixed  $value The value to which to set the attribute
     */
    protected function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Retrieve a local attribute
     *
     * @return mixed
     */
    protected function getAttribute($attribute)
    {
        return (isset($this->attributes[$attribute])) ? $this->attributes[$attribute] : NULL;
    }
}
