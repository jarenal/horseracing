<?php

require_once __DIR__."/../config/autoload.php";

use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;

$messagesHandler = $container->get(\Jarenal\App\WebSocket\MessagesHandler::class);
$ws = new WsServer($messagesHandler);
$server = IoServer::factory(new HttpServer($ws), 8080);
$server->run();
