<?php

namespace Np\Structure\Classes;

class PhoneSms
{
    public static function send($message, $number): array
    {

        $url = getenv('SMS_URL');
        $username = getenv('SMS_USERNAME');
        $password = getenv('SMS_PASSWORD');
        $acode = getenv('SMS_ACODE');
        $smsInfo = $message;
        $masking = getenv('SMS_MASKING');
        $msisdn = array($number);

        $params = array(
            "auth" => array(
                "username" => $username,
                "password" => $password,
                "acode" => $acode,
            ),
            "smsInfo" => array(
                "message" => $smsInfo,
                "is_unicode" => 0,
                "masking" => $masking,
                "msisdn" => $msisdn,
            ),
        );

        $content = json_encode($params);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $result = curl_exec($curl);
        curl_close($curl);
        return [
            'success' => true,
            'response' => $result
        ];
    }
}
