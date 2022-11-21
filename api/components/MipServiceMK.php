<?php

namespace api\components;

use common\models\model\Profile;
use GuzzleHttp\Client;
use yii\httpclient\Client as HttpClient;

class MipServiceMK
{
    public $user_number = '';
    public $numbers_array = [];

    private $_token = 'BF9F9B0C-9273-4072-A815-A51AC905FE9A';

    public static function corrent($profile)
    {
        $pin = $profile->passport_pin;
        $document_issue_date = $profile->passport_given_date;

        $data = [];
        $error = '';
        $data['status'] = false;


        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => 'BF9F9B0C-9273-4072-A815-A51AC905FE9A',
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "adliya.get_personal_data_by_pin",
                    "params" => [
                        "pin" => $pin,
                        "document_issue_date" => $document_issue_date
                    ]
                ]
            )]
        );

        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());
            if (isset($res->result)) {
                $result = $res->result;

                $photo = self::saveTo($result->photo, $result->pinpp, $result->namelatin, $result->surnamelatin);
                // dd(json_decode($response->getBody()->getContents()));
                // return  json_decode($response->getBody()->getContents());
                $data['status'] = true;
                $result->avatar = $photo;

                $profile->passport_seria = $result->doc_seria;
                $profile->passport_number = $result->doc_number;
                $profile->last_name = $result->surnamelatin;
                $profile->first_name = $result->namelatin;
                $profile->middle_name = $result->patronymlatin;
                $profile->passport_issued_date = $result->docdateend;
                $profile->birthday = $result->birthdate;
                $profile->gender = ($result->sex == 1) ? 1 : 0;
                $profile->image = $result->avatar;
                $profile->checked_full = 1;
                if (!$profile->save(false)) $error = $profile->errors;

                $data['data'] = $result;
                $data['error'] = $error;

                return [$pin => true];
                return $data;
            } else {
                $error = $res->error;
                $data['error'] = $error;
                return [$pin => false];
                return $data;
            }
        } else {
            $data['status'] = false;
            return [$pin => false];
            return $data;
        }
    }
    public static function getData($pin, $document_issue_date)
    {
        // $pin = "61801045840029";
        // $document_issue_date =  "2021-01-13";

        $data = [];
        $error = [];
        $data['status'] = false;


        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Api-token' => 'BF9F9B0C-9273-4072-A815-A51AC905FE9A',
            ]
        ]);

        $response = $client->post(
            'http://10.190.24.138:7075',
            ['body' => json_encode(
                [
                    'jsonrpc' => '2.2',
                    "id" => "ID",
                    "method" => "adliya.get_personal_data_by_pin",
                    "params" => [
                        "pin" => $pin,
                        "document_issue_date" => $document_issue_date
                    ]
                ]
            )]
        );

        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());
            if (isset($res->result)) {
                $result = $res->result;

                $photo = self::saveTo($result->photo, $result->pinpp);
                // dd(json_decode($response->getBody()->getContents()));
                // return  json_decode($response->getBody()->getContents());
                $data['status'] = true;
                $result->avatar = $photo;
                $profile = Profile::findOne(['passport_pin' => $result->pinpp]);
                if ($profile) {
                    $profile->passport_seria = $result->doc_seria;
                    $profile->passport_number = $result->doc_number;
                    $profile->last_name = $result->surnamelatin;
                    $profile->first_name = $result->namelatin;
                    $profile->middle_name = $result->patronymlatin;
                    $profile->passport_issued_date = $result->docdateend;
                    $profile->birthday = $result->birthdate;
                    $profile->gender = ($result->sex == 1) ? 1 : 0;
                    $profile->image = $result->avatar;
                    $profile->checked_full = 1;
                    if (!$profile->save(false)) $error = $profile->errors;
                }

                $data['data'] = $result;
                $data['error'] = $error;

                return $data;
            } else {
                $error[] = $res->error;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }
    }

    private static function saveTo($imgBase64, $pin)
    {
        // $imgBase64 = '';
        $uploadPathMK   = STORAGE_PATH  . 'user_images/';
        if (!file_exists(STORAGE_PATH  . 'user_images/')) {
            mkdir(STORAGE_PATH . 'user_images/', 0777, true);
        }

        $parts        = explode(
            ";base64,",
            $imgBase64
        );
        $imageparts   = explode("image/", @$parts[0]);
        $imagebase64  = base64_decode($imgBase64);
        $miniurl = $pin . '.png';
        $file = $uploadPathMK . $miniurl;

        file_put_contents($file, $imagebase64);

        return 'storage/user_images/' . $miniurl;
    }

    private static function saveToTurniket($imgBase64, $pin, $first_name, $last_name)
    {
        // $imgBase64 = '';
        $uploadPathMK   = STORAGE_PATH  . 'user_images/';
        if (!file_exists(STORAGE_PATH  . 'user_images/')) {
            mkdir(STORAGE_PATH . 'user_images/', 0777, true);
        }

        $parts        = explode(
            ";base64,",
            $imgBase64
        );
        $imageparts   = explode("image/", @$parts[0]);
        $imagebase64  = base64_decode($imgBase64);
        $miniurl = $last_name . "+" . $first_name . "_" . $pin . '.png';
        $file = $uploadPathMK . $miniurl;

        file_put_contents($file, $imagebase64);

        return 'storage/user_images/' . $miniurl;
    }
}
