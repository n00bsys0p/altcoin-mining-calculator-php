<?php

namespace n00bsys0p;

require_once('TemplateProcessor.php');

class ViewBuilder
{
    protected $processor = NULL;

    public function __construct()
    {
        $this->processor = new TemplateProcessor;
    }

    /**
     * Loop through the fiat variables, generating the required HTML
     * to insert into the table - both for headings and values
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

    public function prepareBody($data)
    {
        $body = $this->generateTemplate('body', $data);

        $suffix = $data['HASHSUFFIX'];
        // Now choose which option is already selected
        $selected_regex = '/>' . preg_replace('/\//', '\/', $suffix) . '/';
        $body = preg_replace($selected_regex, ' selected="selected">' . $suffix, $body);

        return $body;
    }

    public function prepareLayout($data)
    {
        return $this->generateTemplate('main', $data);
    }

    protected function generateTemplate($template, $body_vars)
    {
        $output = $this->processor->process($template, $body_vars);

        return $output;
    }
}
