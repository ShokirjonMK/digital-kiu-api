<?php

namespace api\components;

class HemisMK
{
    protected $urlToken = 'http://ministry.hemis.uz/app/rest/v2/oauth/token';
    protected $urlStudent = 'http://ministry.hemis.uz/app/rest/v2/services/student/get?pinfl=';
    protected $userName = 'ulaw';
    protected $password = 'tmeF3qFKmet8Y7D';

    public function getHemis($pinfl)
    {
        $url = $this->urlStudent . $pinfl;
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        // $token = self::getToken();

        // return $token;
        $token = 'VB2guh8PQPOhW5z-GcMBf7Dp7aA';
        // headers
        $headers = array(
            "Authorization: Bearer $token",
            'Content-Type: application/json; charset=UTF-8'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        // curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($mk_curl, CURLOPT_POSTFIELDS, $xmlMK);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($mk_curl);

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {

            // return $response;


            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);

            return $getContentJson;
        }
        return $pinfl;
    }

    public function getToken()
    {
        // headers
        $headers = array(
            "Authorization: Basic Y2xpZW50OnNlY3JldA==",
            'Content-Type: multipart/form-data; boundary=<calculated when request is sent>'
        );


        $defaults = array(
            CURLOPT_URL => $this->urlToken,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic Y2xpZW50OnNlY3JldA==",
                'Content-Type: multipart/form-data; boundary=<calculated when request is sent>'
            ),
            CURLOPT_HEADER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'grant_type' => 'password',
                'username' => $this->userName,
                'password' => $this->password
            ),
            CURLOPT_TIMEOUT => 30,
        );

        $mk_curl = curl_init();
        curl_setopt_array($mk_curl, $defaults);


        $response = curl_exec($mk_curl);

        // return $response;
        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {

            return $response;
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);

            return $getContentJson;
        }
        return false;
    }
}
