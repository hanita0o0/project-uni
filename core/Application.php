<?php
namespace app\core;
class Application
{

    public static $app ;
    public static $root_dir;
    public Router $router;
    public Response $response;
    public Request $request;
    public Database $db;
    public Controller $controller;
    public JwtHandler $jwt;

    public function __construct($rootPath,array $config)
    {
        self::$root_dir = $rootPath;
        self::$app = $this;
        $this->router = new Router();
        $this->response = new Response();
        $this->request = new Request();
        $this->db = new Database($config['db']);
        $this->jwt = new JwtHandler();

    }
    public function run(){
        echo $this->router->resolve();
    }


}