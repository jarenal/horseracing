<?php

namespace Jarenal\Core;

use DI\Annotation\Inject;
use Jarenal\Core\Router;

class Kernel
{
    /**
     * @Inject("Jarenal\Core\Router")
     * @var \Jarenal\Core\Router $router
     */
    private $router;

    public function run()
    {
        $controller = $this->router->getController();
        $output = call_user_func([$controller, $this->router->getAction()]);
        exit($output);
    }
}
