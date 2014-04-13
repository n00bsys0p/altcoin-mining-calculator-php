<?php

namespace n00bsys0p;

class TemplateProcessor
{
    public static function process($title, $body)
    {
        $template = file_get_contents(getcwd() . '/tpl/main.tpl.html');

        $template = preg_replace('/\{\{TITLE\}\}/', $title, $template);
        $template = preg_replace('/\{\{BODY\}\}/', $body, $template);

        return $template;
    }
}
