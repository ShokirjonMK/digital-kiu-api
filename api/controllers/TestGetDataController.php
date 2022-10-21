<?php

namespace api\controllers;

use api\components\HemisMK;
use api\components\MipService;
use api\components\MipServiceMK;
use api\components\MipTokenGen;
use api\components\PersonDataHelper;

use base\ResponseStatus;
use common\models\model\LoginHistory;

class TestGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\TestGetData';

    public function actions()
    {
        return [];
    }

    public function actionHemis($pinfl)
    {
        $hemis = new HemisMK();

        $data = $hemis->getHemis($pinfl);
        // return $data;
        if ($data->success) {
            return $this->response(1, _e('Success'), $data->data);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $data->data, ResponseStatus::FORBIDDEN);
        }
    }

    public function actionIndex($passport = null, $jshir = null)
    {

        return MipServiceMK::getData();
        // Define the Base64 value you need to save as an image
        $base64string = "/9j/4AAQF9\nPSn4FR4NLg+tID//2Q==";

        // $base64string = '';
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
        //    return LoginHistory::createItemLogin();

        // return getIpAddressData();

        // return 1;
        // $data = MipTokenGen::getToken();
        $pinpp = "60111016600035";
        $doc_give_date = "2017-09-28";

        $data = MipService::getPhotoService($pinpp, $doc_give_date);

        return $this->response(1, _e('Success'), $data);


        $mk = new MipService();


        // $xml = simplexml_load_string($mk->getPhotoService($pinpp, $doc_give_date)); // where $xml_string is the XML data you'd like to use (a well-formatted XML string). If retrieving from an external source, you can use file_get_contents to retrieve the data and populate this variable.
        // $json = json_encode($xml); // convert the XML string to JSON
        // $array = json_decode($json, TRUE);


        /*  $xmlObject = simplexml_load_string($mk->getPhotoService($pinpp, $doc_give_date));

        //Encode the SimpleXMLElement object into a JSON string.
        $jsonString = json_encode($xmlObject);

        //Convert it back into an associative array for
        //the purposes of testing.
        $jsonArray = json_decode($jsonString, true);

        //var_dump out the $jsonArray, just so we can
        //see what the end result looks like
        return $jsonArray;


        return $array ; */

        $rrrr = $mk->getPhotoService($pinpp, $doc_give_date);

        return $rrrr;
        return simplexml_load_file($rrrr);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new PersonDataHelper();
        //  $data = $model->services($jshir, $passport);
        $data = $model->services("30505985280023", "AA7231228");
        if (empty($data)) {
            return 'error-no';
        } else {
            return $data;
        }
    }

    public function actionView()
    {
    }
}
