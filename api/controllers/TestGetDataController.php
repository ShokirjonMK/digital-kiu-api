<?php

namespace api\controllers;

use api\components\MipService;
use api\components\PersonDataHelper;
use common\models\model\TeacherAccess;
use Yii;
use base\ResponseStatus;

class TestGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\TestGetData';

    public function actions()
    {
        return [];
    }

    public function actionIndex($passport = null, $jshir = null)
    {

        // $str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
        // $order   = array("\r\n", "\n", "\r");
        // $replace = '<br />';

        // return str_replace($order, $replace, $str);

        $mk = new MipService();
        $pinpp = "30111975890051";
        $doc_give_date = "2014-12-09";
        return $mk->getPhotoService($pinpp, $doc_give_date);

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
