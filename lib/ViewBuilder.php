<?php

namespace n00bsys0p;

require_once('TemplateProcessor.php');

/**
 * View Builder
 *
 * This class is very much tied into the display of this
 * specific instance of the calculator. It provides functions
 * which build each distinct section of the displayed content.
 */
class ViewBuilder
{
    protected $processor = NULL;

    /**
     * Constructor
     *
     * Initialises the class
     */
    public function __construct()
    {
        $this->processor = new TemplateProcessor;
    }

    /**
     * Prepare the fiat headers partial view
     *
     * Loop through the fiat variables, generating the required HTML
     * to insert into the view.
     *
     * @param  array  $data An associative array of fiat codes to symbols
     * @return string
     */
    public function prepareFiatHeaders($data)
    {
        $fiat_hdr = '';
        foreach($data as $code3 => $symbol)
        {
            $vars = array('FIAT' => $code3,
                          'SYMBOL' => $symbol);

            $fiat_hdr .= $this->generateTemplate('fiat_header', $vars);
        }

        return $fiat_hdr;
    }

    /**
     * Prepare the fiat values partial view
     *
     * Similar to preparing the headers, the values must be inserted
     * into the template partial.
     *
     * @param  array  $data An associative array of currency codes and related values
     * @return string
     */
    public function prepareFiatValues($data)
    {
        $fiat_val = '';
        foreach($data as $code3 => $value)
        {
            $vars = array('FIAT' => $code3,
                          'VALUE' => $value);

            $fiat_val .= $this->generateTemplate('fiat_value', $vars);
        }

        return $fiat_val;
    }

    /**
     * Prepare the main body of the page
     *
     * Prepare the rest of the page. This fills all extraneous and
     * informational details into the main body of the page.
     *
     * @param  array  $data An associative array of data to fill out
     * @return string
     */
    public function prepareBody($data)
    {
        $body = $this->generateTemplate('body', $data);

        $suffix = $data['HASHSUFFIX'];
        // Now choose which option is already selected
        $selected_regex = '/>' . preg_replace('/\//', '\/', $suffix) . '/';
        $body = preg_replace($selected_regex, ' selected="selected">' . $suffix, $body);

        return $body;
    }

    /**
     * Generate a template based on the main page layout.
     *
     * Generates the main page. $data should be an associative
     * array with keys for each area that needs filling out in
     * the main page template.
     */
    public function prepareLayout($data)
    {
        return $this->generateTemplate('main', $data);
    }

    /**
     * Generate a template
     *
     * Shell out processing work to the built-in Template Processor
     *
     * @param string $template  The name of the template, without any file exension
     * @param array  $body_vars The associative array of sections to replace and the data due for them
     */
    protected function generateTemplate($template, $body_vars)
    {
        $output = $this->processor->process($template, $body_vars);

        return $output;
    }
}
