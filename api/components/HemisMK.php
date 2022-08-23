<?php

namespace api\components;


class HemisMK
{
    public static function getHemis($pinfl)
    {
        $url = 'http://ministry.hemis.uz/app/rest/v2/services/student/get?pinfl=' . $pinfl;
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        // $token = MipTokenGen::getToken('token');
        $token = 'gi58N1x6pJXylNje_7In06QHpu0';
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
}
