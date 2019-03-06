<?php

require_once __DIR__."/../config/autoload.php";

$kernel = $container->get(\Jarenal\Core\Kernel::class);
$kernel->run();

