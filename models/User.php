<?php

namespace app\models;
use app\core\Model;

class User extends Model
{
    public string $mobile="";
    public string $verification_code="";
    public int    $verified = 0;
    public string $username="";
    public string $firstname="";
    public string $lastname="";
    public string $api_token="";
    public string $email="";
    public string $introduced_no="";
    public int $wallet = 0;
    public string $image="";





}
