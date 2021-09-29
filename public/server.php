<?php

//load autoload file for autoload install all classes and ...
use app\core\Chat;
use app\core\Database;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require_once  __DIR__ . "/../vendor/autoload.php";

//load .env file
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8081
);

$server->run();