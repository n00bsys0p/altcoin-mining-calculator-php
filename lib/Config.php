<?php

namespace n00bsys0p;

use \Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * Loop through all .yml files in the given folder
     * and return an array of all of them, indexed by
     * stripped filename.
     */
    public static function parse($config_dir)
    {
        $config = array();
        $config_files = glob($config_dir . '/*.yml');

        foreach($config_files as $file)
        {
            // Key is filename without folder and with extension trimmed
            $key = preg_replace('/^.*\//', '', explode('.', $file)[0]);
            $config[$key]= Yaml::parse($file);
        }

        return $config;
    }
}
