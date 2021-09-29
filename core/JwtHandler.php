<?php
namespace app\core;
require dirname(__DIR__).'/vendor/firebase/php-jwt/src/JWT.php';
require dirname(__DIR__).'/vendor/firebase/php-jwt/src/ExpiredException.php';
require dirname(__DIR__).'/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
require dirname(__DIR__).'/vendor/firebase/php-jwt/src/BeforeValidException.php';

use \Firebase\JWT\JWT;

class JwtHandler {
    protected $jwt_secrect;
    protected $token;
    protected $issuedAt;
    protected $expire;
    protected $jwt;

    public function __construct()
    {
        // set your default time-zone
        date_default_timezone_set('Asia/Tehran');
        $this->issuedAt = time();

        // Token Validity (3600 second =60min)
        $this->expire = $this->issuedAt + 3600;

        // Set your secret or signature
        $this->jwt_secrect = $_ENV['SECRET_KEY'];
    }

    // ENCODING THE TOKEN
    public function _jwt_encode_data($data){

        $this->token = array(
            //Adding the identifier to the token (who issue the token)
//            "iss" => $iss,
//            "aud" => $iss,
            // Adding the current timestamp to the token, for identifying that when the token was issued.
            "iat" => $this->issuedAt,
            // Token expiration
            "exp" => $this->expire,
            // Payload
            "data"=> $data
        );

        $this->jwt = JWT::encode($this->token, $this->jwt_secrect);
        return $this->jwt;

    }

    //DECODING THE TOKEN
    public function _jwt_decode_data($jwt_token){
        try{
            $decode = JWT::decode($jwt_token, $this->jwt_secrect, array('HS256'));
            return $decode->data;
        }
        catch(\Firebase\JWT\ExpiredException $e){
            echo 'Token exception: ', $e->getMessage();
        }
        catch(\Firebase\JWT\SignatureInvalidException $e){
            echo 'Token exception: ', $e->getMessage();
        }
        catch(\Firebase\JWT\BeforeValidException $e){
            echo 'Token exception: ', $e->getMessage();
        }
        catch(\DomainException $e){
            echo 'Token exception: ', $e->getMessage();
        }
        catch(\InvalidArgumentException $e){
            echo 'Token exception: ', $e->getMessage();
        }
        catch(\UnexpectedValueException $e){
            echo 'Token exception: ', $e->getMessage();
        }

    }
}