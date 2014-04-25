<?php

namespace n00bsys0p;

require_once(APP_DIR . '/lib/CachingHttpClient.php');

/**
 * Exchange rate converter
 * 
 * An exchange rate converter that uses www.likeforex.com to
 * do the conversions. The site doesn't provide a JSON API, so
 * we have to parse the details from the HTML widget.
 */
class ExchangeRateConverter
{
	protected $client = NULL;

	/**
	 * Constructor
	 * 
	 * Initialise the HTTP client
	 */
	public function __construct()
	{
		$this->client = new CachingHttpClient;
	}

	/**
	 * Convert currencies between each other
	 * 
	 * This converts a quantity of source fiat currency to a
	 * destination fiat currency, given the 3 character currency
	 * code and an amount to convert.
	 * 
	 * @param  string $from   The currency code to convert from
	 * @param  string $to     The currency code to convert to
	 * @param  float  $amount The quantity to convert
	 * @return float
	 */
	public function convert($from, $to, $amount)
	{
		if(!preg_match('/^[A-Z]{3}$/', $from))
			throw new \Exception('Invalid source currency format.');

		if(!preg_match('/^[A-Z]{3}$/', $to))
			throw new \Exception('Invalid destination currency format.');

		$amount = urlencode($amount);
		$fromCode = urlencode($from);
		$toCode = urlencode($to);

		$url = "http://www.likeforex.com/widget-exchange-rates.php?s=$fromCode,$toCode;";
		$response = $this->client->get($url, 'curr_' . $from . '_' . $to . '.dat', array('max-age' => CACHE_FIAT_TIMEOUT));

		$value = $this->parseCurrencyValue($response);
		$result = $value * $amount;

		// Return in fiat currency format
		return sprintf('%.02f', $result);
	}

	/**
	 * Parse the value from an HTML string
	 * 
	 * @param  string $htmlStr The HTML content to be parsed
	 * @return float
	 */
	protected function parseCurrencyValue($htmlStr)
	{
		$html = \Sunra\PhpSimple\HtmlDomParser::str_get_html($htmlStr);
		foreach($html->find('tr td[align=right]') as $d)
		{
		    if(preg_match('/[0-9](\.)?.*[A-Z]{3}/', $d->innertext))
		        return preg_replace('/\ <.*/', '', $d->innertext);
		}

		// If all else fails
		return 0;
	}
}