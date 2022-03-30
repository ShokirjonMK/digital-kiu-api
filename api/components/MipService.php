<?php

namespace api\components;

use common\models\IpsGovUz;
use common\models\IpsService;
use common\models\Logging;
use phpDocumentor\Reflection\Types\False_;
use Yii;
use SoapClient;
use SoapFault;
use yii\console\Exception;

class MipService
{
    public $user_number = '';
    public $numbers_array = [];


    public static function getToken($type = null)
    {
        $username = 'M_fM1f5fxdS0XXjBBLBH79cJ8kIa';
        $password = 'VhOOBPrpPiPI_G00cls0ENP5frUa';
        $url = 'https://iskm.egov.uz:9444/oauth2/token?grant_type=password&username=justice-user2&password=KN8akqXsEg';
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        // headers
        $headers = array(
            "Authorization: Basic TV9mTTFmNWZ4ZFMwWFhqQkJMQkg3OWNKOGtJYTpWaE9PQlBycFBpUElfRzAwY2xzMEVOUDVmclVh",
            'Content-Type: application/json',
            'Content-Length: 0',
            'Accept: application/json'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // Authorization set basic auth
        curl_setopt($mk_curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // Basic Auth username and password
        curl_setopt($mk_curl, CURLOPT_USERPWD, $username . ":" . $password);
        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        // curl execute (get response)
        $response = curl_exec($mk_curl);

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);
            if ($type == "token" || $type == "access_token") {
                return $getContentJson->access_token;
            } elseif ($type == "token_type") {
                return $getContentJson->token_type;
            } elseif ($type == "refresh_token") {
                return $getContentJson->refresh_token;
            } elseif ($type == "expires_in" || $type == "expired" || $type == "expired_at") {
                return $getContentJson->expires_in;
            }
            return $getContentJson;
        }
    }

    public static function getPhotoService()
    {

        $pinpp = "30111975890051";
        $doc_give_date = "2014-12-09";


        $xml = "<?xml version='1.0' encoding=\"utf-8\"?>
                        <DataCEPRequest>
                             <pinpp>$pinpp</pinpp>
                             <document>$doc_give_date</document>
                             <langId>3</langId>
                        </DataCEPRequest>";
        $array = [
            'AuthInfo' => [
                'WS_ID' => '',
                'LE_ID' => '',
            ],
            'Data' => $xml,
            'Signature' => '',
            'PublicCert' => '',
            'SignDate' => '',
        ];

        $xmlMK = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:idm="http://fido.com/IdmsEGMICServices">
   <soapenv:Header/>
   <soapenv:Body>
      <idm:GetDataByPinppRequest>
         <idm:Data><![CDATA[<?xml version="1.0"?>
         <DataByPinppRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="file:///d:/STS/workspaceEASU/IdmsEGMICServices/src/main/resources/xsdData/GetDatabyDoc.xsd">
         <pinpp>' . $pinpp . '</pinpp>
         <doc_give_date>' . $doc_give_date . '</doc_give_date>
         <langId>1</langId>
         <is_consent_pers_data>Y</is_consent_pers_data>
         </DataByPinppRequest>]]></idm:Data>
         <idm:Signature></idm:Signature>
         <idm:PublicCert></idm:PublicCert>
         <idm:SignDate></idm:SignDate>
      </idm:GetDataByPinppRequest>
   </soapenv:Body>
</soapenv:Envelope>';




    }
}
