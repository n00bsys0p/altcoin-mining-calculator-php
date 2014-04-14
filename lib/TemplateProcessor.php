<?php

namespace n00bsys0p;

class TemplateProcessor
{
    public static function processOld($title, $body)
    {
        $template = file_get_contents(APP_DIR . '/tpl/main.tpl.html');

        $template = preg_replace('/\{\{TITLE\}\}/', $title, $template);
        $template = preg_replace('/\{\{BODY\}\}/', $body, $template);

        return $template;
    }

    public static function process($template, $body_vars = array())
    {
        $view = file_get_contents(APP_DIR . '/tpl/' . $template . '.tpl.html');

        if(is_array($body_vars) && count($body_vars) > 0)
        {
            foreach($body_vars as $search => $replace)
            {
                if(!is_array($replace))
                    $view = preg_replace('/\{\{' . $search . '\}\}/', $replace, $view);
            }
        }

        return $view;
    }
}
