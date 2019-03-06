<?php

namespace Jarenal\Core;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private $settings;

    public function __construct($config_file)
    {
        $this->settings = Yaml::parseFile($config_file, Yaml::PARSE_CONSTANT);
    }

    public function get($param)
    {
        return $this->settings[$param];
    }
}
