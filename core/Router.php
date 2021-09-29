<?php


namespace app\core;


class Router
{
    protected array $routs = [];

    public function get($path,$callback){
        $this->routs['get'][$path] = $callback;

    }
    public function post($path,$callback){
        $this->routs['post'][$path] = $callback;
    }

    public function resolve(){
        $path = Application::$app->request->getPath();
        $method = Application::$app->request->getMethod();
        $callback = $this->routs[$method][$path] ?? false;
        if($callback === false){
            Application::$app->response->setStatusCode('404');
            return '404';
        }
        if(is_array($callback)){
            Application::$app->controller = new $callback[0];
            $callback[0] =  Application::$app->controller;
        }
            return call_user_func($callback);
//        }
    }

}