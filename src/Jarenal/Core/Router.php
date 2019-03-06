<?php

namespace Jarenal\Core;

use DI\Annotation\Inject;
use DI\Container;

class Router
{
    private $config;

    private $routes;

    private $controller;

    private $action;

    private $container;

    /**
     * Inject
     * @param Config $config
     * @param Container $container
     */
    public function __construct($config, $container)
    {
        $this->config = $config;
        $this->routes = $this->config->get("routes");
        $this->container = $container;

        foreach ($this->routes as $route) {
            $pattern = str_replace("/", "\/", $route["pattern"]);
            if (preg_match("/^".$pattern."$/", $_SERVER["REQUEST_URI"]) && ($_SERVER["REQUEST_METHOD"] === $route["method"])) {
                $this->controller = $this->container->get($route["controller"]);
                $this->action = $route["action"];
            }
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }
}
