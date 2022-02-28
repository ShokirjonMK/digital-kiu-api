<?php

namespace api\controllers;

use api\components\PersonDataHelper;
use common\models\model\TeacherAccess;
use Yii;
use base\ResponseStatus;

class TestGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\TeacherAccess';

    public function actions()
    {
        return [];
    }


    public function actionIndex($passport = null, $jshir = null)
    {
//        return 0;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new PersonDataHelper();
      //  $data = $model->services($jshir, $passport);
        $data = $model->services("30505985280023","AA7231228");
        if (empty($data)) {
            return 'error-no';
        } else {
            return $data;

        }

    }


}
