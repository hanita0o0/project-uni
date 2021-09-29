<?php
/*--- Register The Auto Loader -----
 Composer provides a convenient, automatically generated
 class loader for our application.*/
use app\core\Application;
//load autoload file for autoload install all classes and ...
require_once  __DIR__ . "/vendor/autoload.php";
//load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$config = [
    'db'=>[
        'dsn' => $_ENV['DB_DSN'],
        'user'=> $_ENV['DB_USERNAME'],
        'password'=>$_ENV['DB_PASSWORD']
    ]
];
$app = new Application(__DIR__,$config);
$app->db->applyMigrations();
