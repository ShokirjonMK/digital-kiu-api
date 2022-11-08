<?php

namespace api\controllers;

use api\components\HemisMK;
use api\components\MipService;
use api\components\MipServiceMK;
use api\components\MipTokenGen;
use api\components\PersonDataHelper;

use base\ResponseStatus;
use common\models\model\Attend;
use common\models\model\Chichqoq;
use common\models\model\LoginHistory;
use common\models\model\StudentAttend;
use common\models\model\SubjectContent;

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

    // public function actionIndex111($i)
    // {
    //     $errors = [];
    //     $soni = $i * 500;

    //     // return $this->response(1, _e('Success'), $soni);

    //     $attends = Attend::find()
    //         ->limit(500)->offset($soni)->all();


    //     foreach ($attends as $one) {


    //         foreach ($one->student_ids as $student_id) {
    //             /** new Student Attent here */

    //             /** Checking student is really choos this time table */

    //             /** Checking student is really choos this time table */
    //             $newStudentAttend = new StudentAttend();
    //             $newStudentAttend->student_id = $student_id;
    //             $newStudentAttend->attend_id = $one->id;
    //             $newStudentAttend->time_table_id = $one->time_table_id;
    //             $newStudentAttend->subject_id = $one->subject_id;
    //             $newStudentAttend->date = $one->date;
    //             $newStudentAttend->subject_category_id = $one->subject_category_id;
    //             $newStudentAttend->edu_year_id = $one->edu_year_id;
    //             $newStudentAttend->time_option_id = $one->time_option_id;
    //             $newStudentAttend->edu_semestr_id = $one->edu_semestr_id;
    //             $newStudentAttend->faculty_id = $one->faculty_id;
    //             $newStudentAttend->course_id = $one->timeTable->course_id;
    //             $newStudentAttend->edu_plan_id = $one->edu_plan_id;
    //             $newStudentAttend->type = $one->type;
    //             $newStudentAttend->semestr_id = $one->eduSemestr->semestr_id;

    //             $newStudentAttend->updated_by = $one->updated_by;
    //             $newStudentAttend->created_by = $one->created_by;
    //             $newStudentAttend->updated_at = $one->updated_at;
    //             $newStudentAttend->created_at = $one->created_at;

    //             // return $this->response(1, _e('Success'), $newStudentAttend->created_at);

    //             // $newStudentAttend->reason = $one->reason;
    //             if (!$newStudentAttend->save()) {
    //                 $errors[] = [$student_id => $newStudentAttend->errors];
    //             }
    //             /** new Student Attent here */
    //         }
    //     }

    //     return $this->response(1, _e('Success'), $errors);
    // }



    /*  public function actionChich($passport = null, $jshir = null)
    {
        $chich = Chichqoq::find()->all();
        $errors = [];
        foreach ($chich as $ch) {
            $sub_content = SubjectContent::findAll(['subject_topic_id' => $ch->a1]);

            foreach ($sub_content as $s_c) {
                $new = new SubjectContent();

                $new->content = $s_c->content;
                $new->type = $s_c->type;
                $new->subject_id = $ch->a4;
                $new->user_id = $s_c->user_id;
                $new->subject_topic_id = $ch->a2;
                $new->teacher_access_id = $s_c->teacher_access_id;
                $new->description = $s_c->description;
                $new->file_url = $s_c->file_url;
                $new->order = $s_c->order;
                $new->status = $s_c->status;
                $new->created_at = $s_c->created_at;
                $new->updated_at = $s_c->updated_at;
                $new->created_by = $s_c->created_by;
                $new->updated_by = $s_c->updated_by;
                $new->is_deleted = $s_c->is_deleted;

                if (!$new->save()) {
                    $errors[] = $new->errors;
                }
            }
        }

        return $this->response(1, _e('Success'), $errors);
    } */

    public function actionIndex($passport = null, $jshir = null)
    {

        HemisMK::getHemis(61801045840029);
        $mip = MipServiceMK::getData(61801045840029, "2021-01-13");
        if ($mip['status']) {
            return $this->response(1, _e('Success'), $mip['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $mip['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }


        $pin = "61801045840029";
        $document_issue_date =  "2021-01-13";

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
