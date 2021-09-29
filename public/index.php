<?php
/*--- Register The Auto Loader -----
 Composer provides a convenient, automatically generated
 class loader for our application.*/
use app\core\Application;
use app\controllers\AuthController;
use app\controllers\AuctionController;


//load autoload file for autoload install all classes and ...
require_once  __DIR__ . "/../vendor/autoload.php";

//load .env file
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
$config = [
  'db'=>[
      'dsn' => $_ENV['DB_DSN'],
      'user'=> $_ENV['DB_USERNAME'],
      'password'=>$_ENV['DB_PASSWORD'],
  ],

];

$app = new Application(dirname(__DIR__),$config);

//$app->router->get('/',function(){
// echo "ok";
//}

$app->router->post('/register',[AuthController::class,"register"]);
$app->router->post('/send-otp',[AuthController::class,"sendOtp"]);
$app->router->post('/verify',[AuthController::class,"verify"]);
$app->router->post('/login',[AuthController::class,"login"]);
$app->router->get('/user/show',[AuthController::class,'show']);
$app->router->post('/user/update',[AuthController::class,'update']);
$app->router->post('/user/charge-wallet',[AuthController::class,'charge_wallet']);
$app->router->get('/user/show-wallet',[AuthController::class,'show_wallet']);

$app->router->post('/auction/create',[AuctionController::class,'create']);
$app->router->get('/auction/add-participant',[AuctionController::class,'add_participant']);
$app->router->get('/auction/show',[AuctionController::class,'show']);
$app->router->post('/auction/charge-bid',[AuctionController::class,'charge_bid']);
$app->router->get('/auction/list-old-auctions',[AuctionController::class,'list_old_auctions']);
$app->router->get('/auction/list-auctions',[AuctionController::class,'list_auctions']);
$app->router->get('/auction/winner',[AuctionController::class,'winner']);
$app->router->get('/auction/winners',[AuctionController::class,'winners']);
$app->router->get('/auction/list-participants',[AuctionController::class,'list_participants']);
$app->router->post('/auction/start',[AuctionController::class,'start']);

$app->run();




