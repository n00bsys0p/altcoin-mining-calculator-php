<?php

namespace n00bsys0p;

/**
 * Template Processor
 *
 * This simply provides a static helper to fill out a template
 * file's placeholders with user-provided data.
 */
class TemplateProcessor
{
    /**
     * Insert variables into a template file
     *
     * This will open the template file named $template.tpl.html in the
     * /tpl folder, and replace all occurrences of {{KEY}} with VALUE
     * from the keys and values provided in the associative array $body_vars.
     *
     * @param string $template  The stripped name of the template file. No file extensions.
     * @param array  $body_vars An associative array to replace all instances of each key with the corresponding value
     */
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
