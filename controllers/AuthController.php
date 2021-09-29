<?php


namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\tool\Sms;
use app\models\User;

class AuthController extends Controller
{

    public function sendOtp()
    {
        if (!empty($_POST['mobile'])) {
            $user = new User();
            $user->loadData(Application::$app->request->getBody());
            // Generate random verification code
            $rand_no = rand(1000, 9999);
            // Check previous entry
            $conditions = [
                'mobile' => $user->mobile
            ];
            $checkPrev = $user->checkRow('users', $conditions);

            if ($checkPrev) {
                // send otp code to user mobile
                $data = [
                    'verification_code' => $rand_no
                ];
                $insert = $user->update('users', $data, $conditions);
                if ($insert) {
                    // Send otp to user via SMS
                    $message = "کد تایید در سامانه حراجی : " . $rand_no;

                    $result = Sms::sendSms($user->mobile, $message);
                    if ($result) {
                        $position = strpos($result, ';');
                        return (substr($result, 0, $position));
                    } else {
                        echo "not send sms".PHP_EOL;
                        return 2;
                    }
                } else {
                    echo "not generate verification code in db,problem db".PHP_EOL;
                    return "3";
                }
            } else {
                echo "not register before,so redirect /register api".PHP_EOL;
                return 4;
            }
        } else {
            echo "invalid input".PHP_EOL;
            return 1;
        }
    }

    public function verify()
    {
        if (!empty($_POST['mobile']) && !empty($_POST['verification_code'])) {
            $user = new User();
            $user->loadData(Application::$app->request->getBody());

            $conditions = [
                'mobile' => $user->mobile,
            ];
            $check = $user->checkRow('users', $conditions);
            if ($check) {
                if (!empty($check['verification_code'])) {
                    //so user register before and received sms before
                    $verification_code = $check['verification_code'];
                    if ($verification_code === $user->verification_code) {
                        //verified
                        $data = [
                            'verified' => 1
                        ];
                        $user->update('users', $data, $conditions);
                        //user registered before, so login
                        $token = Application::$app->jwt->_jwt_encode_data(
                            [
                                'id' => $check['id'],
                                'mobile' => $check['mobile'],
                                'username' => $check['username'],
                                'image' =>$check['image']
                            ]
                        );

                        $ret = [
                            'id'=>$check['id'],
                            'username' => $check['username'],
                            'api_token' => $token,
                            'image'=>$check['image'],
                            'mobile'=>$check['mobile']
                        ];

                        return json_encode($ret);

                    } else {
                        //invalid verification code so verified = 0
                        $data = [
                            'verified' => 0
                        ];
                        $user->update('users', $data, $conditions);
                        echo "invalid verification code so verified = 0".PHP_EOL;
                        return 0;
                    }
                } else {
                    echo "not send sms before".PHP_EOL;
                    return 1;
                }
            } else {
                echo "user not registered before,so call /register api".PHP_EOL;
                return $user->mobile;
            }
        } else {
            echo "invalid input".PHP_EOL;
            return 2;
        }


    }

    public function register()
    {
        if (!empty($_POST['mobile']) && !empty($_POST['username'])
            && !empty($_POST['firstname']) && !empty($_POST['lastname'])) {
            $user = new User();
            $user->loadData(Application::$app->request->getBody());
            // check user exist before
            $conditions = [
                'mobile' => $user->mobile,
            ];
            $data = [
                'mobile' => $user->mobile,
                'username' => $user->username,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'introduced_no' => $user->introduced_no

            ];
            $check = $user->checkRow('users', $conditions);

            if ($check) {
                // just update user

                if ($user->update('users', $data, $conditions)) {
                    echo "registered".PHP_EOL;
                    return $user->mobile;

                } else {
                    echo "problem db not register\n";
                    return 1;
                }

            } else {
                //so insert new data and registered
                if ($user->insert('users', $data)) {
                    echo"registered".PHP_EOL;
                    return $user->mobile;
                } else {
                    echo "problem db not register\n";
                    return 1;
                }
            }

        } else {
            echo "invalid input\n";
            return 2;
        }
    }

    public function show()
    {
        if (isset($_GET['token'])) {
            $data = Application::$app->jwt->_jwt_decode_data(trim($_GET['token']));
             if(isset($data)) {
                 $mobile = $data->mobile;
                 $conditions = [
                     //$data is stdclass
                     'mobile' => $mobile
                 ];

                 $user = new User();
                 $check = $user->checkRow('users', $conditions);
                 if ($check) {
                     $data = [
                         'mobile' => $check['mobile'],
                         'username' => $check['username'],
                         'firstname' => $check['firstname'],
                         'lastname' => $check['lastname'],
                         'email' => $check['email'],
                         'introduced_no' => $check['introduced_no'],
                         'wallet' => $check['wallet'],
                         'image' => $check['image']
                     ];
                     return json_encode($data);
                 } else {
                     echo "invalid api_token\n";
                     return 1;
                 }
             }
        } else {
            echo "empty api_token\n";
            return 2;
        }
    }

    public function update()
    {
        if (isset($_GET['token'])) {
            $data = Application::$app->jwt->_jwt_decode_data(trim($_GET['token']));
            if(isset($data)) {
                $mobile = $data->mobile;
                $conditions = [
                    //$data is stdclass
                    'mobile' => $mobile
                ];
                if (!empty($_POST['username']) && !empty($_POST['firstname'])
                    && !empty($_POST['lastname'])) {
                    $user = new User();
                    $user->loadData(Application::$app->request->getBody());
                    $check = $user->checkRow('users', $conditions);
                    if ($check) {
                        $path = "";
                        if (isset($_FILES['image'])) {
                            $file = $_FILES['image'];
                            if ($file['size'] > 5 * 1024 * 1024) {
                                echo "image uploaded more than 5MB\n";
                                return 4;
                            }
                            $image_formats = array('image/png', 'image/jpg', 'image/jpeg');
                            if (!in_array($file['type'], $image_formats)) {
                                echo "file uploaded not image\n";
                                return 5;
                            }
                            $upload_folder = dirname(__DIR__) . "/upload/profile/";
                            $file_location = $upload_folder . basename($_FILES["image"]["name"]);
                            $path = "/upload/profile/" . basename($_FILES["image"]["name"]);
                            if (!move_uploaded_file($_FILES['image']['tmp_name'], $file_location)) {
                                echo "file not uploaded" . PHP_EOL;
                                return 1;
                            };
                        }
                        $data = [
                            'username' => $user->username,
                            'firstname' => $user->firstname,
                            'lastname' => $user->lastname,
                            'email' => $user->email,
                            'introduced_no' => $user->introduced_no,
                            'image' => $path
                        ];
                        if ($user->update('users', $data, $conditions)) {
                            echo "update user" . PHP_EOL;
                            return 0;
                        }

                    } else {
                        echo "user not found" . PHP_EOL;
                        return 2;
                    }
                } else {
                    echo "not enough input" . PHP_EOL;
                    return 3;
                }
            }

        } else {
            echo "empty api_token\n";
        return 6;
        }
    }

    public function charge_wallet(){
        if (isset($_GET['token']) && !empty($_POST['value'])) {
            $data = Application::$app->jwt->_jwt_decode_data(trim($_GET['token']));
            if(isset($data)) {
                $id = $data->id;
                $conditions = [
                    //$data is stdclass
                    'id' => $id
                ];
                $user= new User();
                $check=$user->checkRow('users',$conditions);
                if($check){
                    $wallet = $check['wallet'];
                    $wallet = $wallet + $_POST['value'];
                    $data =[
                        'wallet'=>$wallet
                    ];
                    if($user->update('users',$data,$conditions)){
                        echo "add wallet".PHP_EOL;
                        return $wallet;
                    }else{
                        echo "problem db".PHP_EOL;
                        return 1;
                    }
                }else{
                    echo "user not found".PHP_EOL;
                    return 2;
                }
            }
            }else{
            echo "not enough input".PHP_EOL;
            return 3;
        }

    }

    public function show_wallet(){
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
                    return $check1['wallet'];

                }else{
                    echo "no found user".PHP_EOL;
                    return 2;
                }
            }
        }else{
            echo "not enough input".PHP_EOL;
            return 1;
        }
    }


}