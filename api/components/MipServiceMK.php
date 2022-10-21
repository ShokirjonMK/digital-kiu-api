<?php

namespace api\components;

use GuzzleHttp\Client;
use SoapClient;
use SoapFault;

class MipServiceMK
{
    public $user_number = '';
    public $numbers_array = [];

    private $_token = 'BF9F9B0C-9273-4072-A815-A51AC905FE9A';

    public static function getData()
    {
        $pin = "61801045840029";
        $document_issue_date =  "2021-01-13";

        $client = new \GuzzleHttp\Client();
        $response = $client->post('http://10.190.24.138:7075', [
            'form_params' => [
                'jsonrpc' => '2.2',
                "id" => "ID",
                "method" => "adliya.get_personal_data_by_pin",
                "params" => [
                    "pin" => $pin,
                    "document_issue_date" => $document_issue_date
                ]
            ]
        ]);



        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://10.190.24.138:7075')
            ->addHeaders([
                'content-type' => 'application/json',
                'Api-token' => self::$_token,
            ])->addBody([
                'jsonrpc' => '2.2',
                "id" => "ID",
                "method" => "adliya.get_personal_data_by_pin",
                "params" => [
                    "pin" => $pin,
                    "document_issue_date" => $document_issue_date
                ]
            ])
            ->send();
        if ($response->isOk) {
            return $response->getData();
        }
    }

    public function saveTo()
    {
        $base64string = '';
        $uploadpath   = STORAGE_PATH  . 'base64/';
        if (!file_exists(STORAGE_PATH  . 'base64/')) {
            mkdir(STORAGE_PATH  . 'base64/', 0777, true);
        }


        $parts        = explode(
            ";base64,",
            $base64string
        );
        $imageparts   = explode("image/", @$parts[0]);
        // $imagetype    = $imageparts[1];
        $imagebase64  = base64_decode($base64string);
        $miniurl = uniqid() . '.png';
        $file = $uploadpath . $miniurl;

        file_put_contents($file, $imagebase64);

        return 'storage/base64/' . $miniurl;
    }
}
