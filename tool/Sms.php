<?php

namespace app\tool;

 abstract class Sms
{
    public static function sendSMS($to,$message){

        $requestParams = array(
            'from' => $_ENV['from'],
            'to' => $to,
            'text' => $message,
            'signature' => $_ENV['API_KEY']
        );

        // Merge API url and parameters
        $url = $_ENV['URL_SMS'];
        foreach($requestParams as $key => $val){
            $url .= $key.'='.urlencode($val).'&';
        }
        $url = rtrim($url, "&");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}