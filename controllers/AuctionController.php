<?php


namespace app\controllers;


use app\core\Application;
use app\core\Controller;
use app\models\Auction;
use app\models\User;
use Ratchet\App;


class AuctionController extends Controller
{

    public function create()
    {

        if (isset($_GET['token'])) {
            $data = Application::$app->jwt->_jwt_decode_data(trim($_GET['token']));
            if (isset($data)) {
                $mobile = $data->mobile;

                if ($mobile === $_ENV['ADMIN']) {
                    //so admin
                    if (!empty($_POST['code']) && !empty($_POST['price'])
                        && !empty($_POST['wage']) && !empty($_POST['base_price'])
                        && !empty($_POST['start_at']) && !empty($_POST['price'])
                        && !empty($_POST['registration_cost']) &&
                        !empty($_POST['product_id']) && !empty($_POST['status_id'])
                        && !empty($_POST['capacity_participants'])) {
                        $auction = new Auction();
                        $auction->loadData(Application::$app->request->getBody());

                        $conditions = [
                            'code' => $auction->code
                        ];
                        $check = $auction->checkRow('auctions', $conditions);
                        if (!$check) {

                            $con = [
                                'id' => $auction->product_id
                            ];
                            $row1 = $auction->checkRow('products', $con);
                            if (!$row1) {
                                echo "not exist product" . PHP_EOL;
                                return 5;
                            }
                            $conn1 = [
                                'name' => "برگزارنشده"
                            ];
                            $row2 = $auction->checkRow('status', $conn1);
                            $conn2 = [
                                'name' => "درحال برگزاری"
                            ];
                            $row3 = $auction->checkRow('status', $conn2);
                            if (!$row2 || !$row3) {
                                echo "please insert valid status in status table" . PHP_EOL;
                                return 6;
                            }
                            $st = [$row2['id'], $row3['id']];
                            if (!in_array($auction->status_id, $st)) {
                                echo "invalid status id" . PHP_EOL;
                                return 7;
                            }
                            $data = [
                                'code' => $auction->code,
                                'price' => $auction->price,
                                'wage' => $auction->wage,
                                'base_price' => $auction->base_price,
                                'start_at' => $auction->start_at,
                                'registration_cost' => $auction->registration_cost,
                                'product_id' => $auction->product_id,
                                'status_id' => $auction->status_id,
                                'capacity_participants' => $auction->capacity_participants,

                            ];

                            if ($auction->insert('auctions', $data)) {
                                echo "create auction" . PHP_EOL;
                                $row3 = $auction->checkRow('auctions', $conditions);
                                return $auction->code;
                            } else {
                                echo "problem db not register" . PHP_EOL;
                                return 1;
                            }
                        } else {
                            echo "this auction exist in db" . PHP_EOL;
                            return 2;
                        }
                    } else {
                        echo "not enough input" . PHP_EOL;
                        return 3;
                    }
                } else {
                    echo "invalid access" . PHP_EOL;
                    return 4;
                }
            }
        }

    }

    public function add_participant()
    {
        if (!empty($_GET['auction_id'])) {
            if (isset($_GET['token'])) {
                $data = Application::$app->jwt->_jwt_decode_data(trim($_GET['token']));
                if (isset($data)) {
                    $user_id = $data->id;
                    $conditions = [
                        'id' => $user_id
                    ];
                    $user = new User();
                    $check1 = $user->checkRow('users', $conditions);
                    if ($check1) {
                        $auction_id = $_GET['auction_id'];

                        $db = Application::$app->db;
                        $qq = "SELECT * FROM status WHERE
                           name = 'برگزارنشده'";
                        $row=$db->pdo->prepare($qq);
                        $row->execute();
                        $result = $row->fetch(\PDO::FETCH_ASSOC);
                        $status_id = $result['id'];
                        $conn = [
                            'id' => $auction_id,
                            'status_id'=>$status_id
                        ];
                        $check2 = $user->checkRow('auctions', $conn);
                        if ($check2) {
                            $query = "SELECT COUNT(id) FROM participants";
                            $db = Application::$app->db;
                            $stm = $db->pdo->prepare($query);
                            $stm->execute();
                            $number_participants = $stm->fetch(\PDO::FETCH_ASSOC);
                            $number_participants = $number_participants['COUNT(id)'];
                            $capacity_participants = $check2['capacity_participants'];
                            $allowed_participant = $number_participants + 1 <= $capacity_participants ? true : false;
                            if ($allowed_participant) {
                                //check not add before
                                $condi = [
                                    'auction_id'=>$auction_id,
                                    'user_id'=>$user_id
                                ];
                                $rrw = $user->checkRow('participants',$condi);
                                if($rrw){
                                    echo "user add before  in auction".PHP_EOL;
                                    return 8;
                                }
                                // reduce of wallet the amount registration_cost
                                $registration_cost = $check2['registration_cost'];
                                $wallet = $check1['wallet'];
                                //check enough cash
                                if ($wallet < $registration_cost) {
                                    echo "not enough wallet" . PHP_EOL;
                                    return 5;
                                }
                                $new_wallet = $wallet - $registration_cost;
                                $data = [
                                    'wallet' => $new_wallet
                                ];
                                if ($user->update('users', $data, $conditions)) {

                                    $data = [
                                        'bid_capacity' => 10,
                                        'user_id' => $user_id,
                                        'auction_id' => $auction_id,
                                    ];
                                    if ($user->insert('participants', $data)) {
                                        echo 'add participant' . PHP_EOL;
                                        return 0;
                                    } else {
                                        echo 'problem db not add' . PHP_EOL;
                                        return 1;
                                    }

                                } else {
                                    echo "not reduce wallet problem db" . PHP_EOL;
                                    return 6;
                                }
                            } else {
                                echo "capacity full, can not add participant to auction" . PHP_EOL;
                                return 2;
                            }

                        } else {
                            echo "auction not exist or held before" . PHP_EOL;
                            return 3;
                        }
                    } else {
                        echo "user not found" . PHP_EOL;
                        return 4;
                    }
                }
            }
        } else {
            echo "not enough input" . PHP_EOL;
            return 7;
        }
    }

    public function show()
    {
        if (empty($_GET['auction_code'])) {
            echo "auction code is empty" . PHP_EOL;
            return 1;

        } else {
            $auction_code = $_GET['auction_code'];
            $condition = [
                'code' => $auction_code
            ];
            $auction = new Auction();
            $check = $auction->checkRow('auctions', $condition);
            if (!$check) {
                echo "auction not exist" . PHP_EOL;
                return 2;
            } else {
                $data = [
                    'id' => $check['id'],
                    'code' => $check['code'],
                    'price' => $check['price'],
                    'wage' => $check['wage'],
                    'base_price' => $check['base_price'],
                    'start_at' => $check['start_at'],
                    'registration_cost' => $check['registration_cost']

                ];

                $conn1 = [
                    'id' => $check['product_id']
                ];
                $check2 = $auction->checkRow('products', $conn1);
                $data1 = [
                    'name' => $check2['name'],
                    'model' => $check2['model'],
                    'description' => $check2['description'],
                    'image' => $check2['image']
                ];
                //find other winners
                $conn3 = [
                    'product_id' => $check['product_id']
                ];
                $conn2 = [
                    'id' => $check['status_id']
                ];
                $check3 = $auction->checkRow('status', $conn2);
                $data2 = [
                    'status' => $check3['name']
                ];
                if ($check3['name'] === 'درحال برگزاری' || $check3['name'] === 'برگزارشده') {
                    $query = "SELECT COUNT(id) FROM participants";
                    $db = Application::$app->db;
                    $stm = $db->pdo->prepare($query);
                    $stm->execute();
                    $number_participants = $stm->fetch(\PDO::FETCH_ASSOC);
                    $data['number_participants'] = $number_participants['COUNT(id)'];
                } else {
                    $data['number_participants'] = "";
                }
                $data = array_merge($data, $data1, $data2);
               return json_encode($data);

            }

        }


    }

    public function charge_bid()
    {
        if (!empty($_POST['auction_code']) && !empty($_POST['bid_package_count']))
        {
            if (isset($_POST['token'])) {
                $data = Application::$app->jwt->_jwt_decode_data(trim($_POST['token']));
                if (isset($data)) {
                    $user_id = $data->id;
                    $condition1 = [
                        'id' => $user_id
                    ];
                    $user = new User();
                    $check1 = $user->checkRow('users', $condition1);
                    if ($check1) {
                        $auction_id = $_POST['auction_code'];
                        $condition2 = [
                            'id' => $auction_id
                        ];
                        $check2 = $user->checkRow('auctions',$condition2);
                        if($check2){
                            $registration_cost = $check2['registration_cost'];
                            $condition3 = [
                                'auction_id'=>$auction_id,
                                'user_id'=>$user_id
                            ];
                            $check3 = $user->checkRow('participants',$condition3);
                            if($check3){
                               $bid_package_count = $_POST['bid_package_count'];
                               $cost = $bid_package_count * $registration_cost;
                               //check enough money
                               if( $check1['wallet']>=$cost){
                                   $new_wallet = $check1['wallet'] - $cost;
                                   $data = [
                                       'wallet'=> $new_wallet
                                   ];
                                   //reduce cost of bid package from user's wallet
                                   $result1 = $user->update('users', $data, $condition1);
                                   if($result1){
                                       $bid_capacity_old = $check3['bid_capacity'];
                                       $bid_capacity_new = $bid_capacity_old + ($bid_package_count *10);
                                       $data = [
                                           'bid_capacity'=>$bid_capacity_new
                                       ];
                                       $result2 = $user->update('participants',$data,$condition3);
                                       if($result2){
                                           echo "charge bid".PHP_EOL;
                                           return 0;
                                       }

                                   }
                               }else{
                                   echo "not enough money".PHP_EOL;
                                   return 5;
                               }
                            }else{
                                echo "user not register in auction before".PHP_EOL;
                                return 4;
                            }
                        }else{
                            echo "not found auction".PHP_EOL;
                            return 3;
                        }

                    }else{
                        echo "not found user".PHP_EOL;
                        return 2;
                    }

                }

            }
            else{
                echo "not enough input".PHP_EOL;
                return 1;
            }
        }else{
            echo "not enough input".PHP_EOL;
            return 1;
        }
    }


    public function list_old_auctions(){

        if (empty($_GET['limit']) && empty($_GET['skip'])) {
            echo "not enough input" . PHP_EOL;
            return 1;

        } else {
            $limit = $_GET['limit'];
            $skip = $_GET['skip'];
            $db= Application::$app->db;
            $sqll = "SELECT id FROM status WHERE name = 'برگزارشده'";
            $stmm = $db->pdo->prepare($sqll);
            $stmm->execute();
            $result1 = $stmm->fetch(\PDO::FETCH_ASSOC);
            $status_id = $result1['id'];

            $sql = "SELECT * FROM auctions WHERE status_id=:status_id LIMIT :limit OFFSET :skip";

            $stmt = $db->pdo->prepare($sql);
            $stmt->bindParam(":status_id", $status_id,\PDO::PARAM_INT);
            $stmt->bindParam(":limit", $limit,\PDO::PARAM_INT);
            $stmt->bindParam(":skip", $skip,\PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $data = json_encode($result);
            return $data;

        }

    }

    public function list_auctions(){

        if (empty($_GET['limit']) && empty($_GET['skip'])) {
            echo "not enough input" . PHP_EOL;
            return 1;

        } else {
            $limit = $_GET['limit'];
            $skip = $_GET['skip'];
            $db= Application::$app->db;
            $sql1 = "SELECT id FROM status WHERE name = 'برگزارنشده'";
            $stmm = $db->pdo->prepare($sql1);
            $stmm->execute();
            $result1 = $stmm->fetch(\PDO::FETCH_ASSOC);
            $status1_id = $result1['id'];

            $sql2 = "SELECT id FROM status WHERE name = 'درحال برگزاری'";
            $stmmm = $db->pdo->prepare($sql2);
            $stmmm->execute();
            $result2 = $stmmm->fetch(\PDO::FETCH_ASSOC);
            $status2_id = $result2['id'];

            $sql = "SELECT * FROM auctions WHERE status_id =:status1_id OR status_id =:status2_id  LIMIT :limit OFFSET :skip";
            $stmt = $db->pdo->prepare($sql);
            $stmt->bindParam(":status1_id", $status1_id,\PDO::PARAM_INT);
            $stmt->bindParam(":status2_id", $status2_id,\PDO::PARAM_INT);
            $stmt->bindParam(":limit", $limit,\PDO::PARAM_INT);
            $stmt->bindParam(":skip", $skip,\PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $data = json_encode($result);
            return $data;

        }


    }

    public function winner(){
        if (empty($_GET['auction_code'])) {
            echo "not enough input" . PHP_EOL;
            return 1;

        } else {
            $auction_code = $_GET['auction_code'];
            $auction = new Auction();
            $condition = ['name'=>'برگزارشده'];
            $check = $auction->checkRow('status', $condition);
            $status_id = $check['id'];
            $condition1 = [
                'code'=>$auction_code,
                'status_id'=>$status_id
            ];
            $check1 = $auction->checkRow('auctions', $condition1);
            if ($check1) {
                $winner= $check1['winner'];
                $condition2 = [
                    'mobile'=>$winner
                ];
                $check2 = $auction->checkRow('users', $condition2);
                $data = [
                  'username'=>$check2['username'],
                  'image'=>$check2['image'],
                  'final_price'=>$check1['final_price'],
                  'final_time'=>$check1['final_time']
                ];
                return json_encode($data);
            }else{
                echo "invalid auction".PHP_EOL;
                return 2;
            }
        }
    }

    public function winners(){
        if (empty($_GET['product_id'])) {
            echo "not enough input" . PHP_EOL;
            return 1;

        } else {
            $product_id = $_GET['product_id'];
            $auction = new Auction();
            $condition = ['name'=>'برگزارشده'];
            $check = $auction->checkRow('status', $condition);
            $status_id = $check['id'];

           $db = Application::$app->db;
           $query = "SELECT * FROM auctions WHERE product_id =:product_id AND 
                      status_id = :status_id";
           $stmt = $db->pdo->prepare($query);
           $stmt->bindParam(':product_id',$product_id,\PDO::PARAM_INT);
           $stmt->bindParam(':status_id',$status_id,\PDO::PARAM_INT);
           $stmt->execute();
           $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           $data = [];

           foreach ($result as $item){
               $var = [];
               foreach ($item as $key=>$value){
                  switch ($key){
                      case 'winner':
                          $condition =['mobile'=>$value];
                          $check = $auction->checkRow('users', $condition);
                          $var['winner']=$check['username'];
                          $var['image'] = $check['image'];
                          break;
                      case 'final_price':
                          $var['final_price']=$value;
                          break;
                      case 'final_time':
                          $var['final_time']=$value;
                          break;
                  }
               }
               $data[]=$var;
           }

           return json_encode($data);
        }

    }
    public function list_participants(){
        if (empty($_GET['auction_id'])) {
            echo "not enough input" . PHP_EOL;
            return 1;

        } else {
            $auction_id = $_GET['auction_id'];
            $db = Application::$app->db;
            $query = "SELECT * FROM participants WHERE auction_id=:auction_id";
            $stm = $db->pdo->prepare($query);
            $stm->bindParam(':auction_id',$auction_id,\PDO::PARAM_INT);
            $stm->execute();
            $result = $stm->fetchAll(\PDO::FETCH_ASSOC);
            if($result){
                $data = [];
                foreach ($result as $item){
                    foreach ($item as $key=>$value){
                        if($key=='user_id'){
                            $data[]=$value;
                        }
                    }
                }
                return json_encode($data);
        }else{
           echo "empty list".PHP_EOL;
           return 2;
          }
      }

    }

    public function start(){

         if (empty($_POST['auction_code'])) {
            echo "not enough input" . PHP_EOL;
            return 1;

        } else {
             $auction =new Auction();
             $auction_code = $_POST['auction_code'];
             $condition = [
                 "code" => $auction_code
             ];
             $check1 = $auction->checkRow('auctions',$condition);
             if($check1){
                 $conn = ['name'=>'درحال برگزاری'];
                 $check2 = $auction->checkRow('status',$conn);
                 if($check2){
                     $status_id = $check2['id'];
                     $data = ['status_id'=>$status_id];
                     $row = $auction->update('auctions',$data,$condition);
                     if($row){
                         echo "start auction".PHP_EOL;
                         return 0;
                     }
                 }
             }else{
                 echo "not found auction".PHP_EOL;
                 return 2;
             }

        }
    }
}

