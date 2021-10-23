<?php

namespace api\controllers;

use Yii;
use api\resources\User;
use api\resources\Password;
use base\ResponseStatus;
use common\models\model\EncryptPass;
use common\models\model\Keys;
use common\models\model\PasswordEncrypts;

class PasswordController extends ApiActiveController
{
    public $modelClass = 'api\resources\Password';

    // public $modelClass;

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $data = new Password();
        $data = $data->decryptThisUser();
        return $this->response(1, _e('Success'), $data);
    }

    public function actionUpdate($lang, $id)
    {
       
        return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        
    }

    public function actionView($lang, $id)
    {
        $user_id = $id;
        $data = new Password();
        $data = $data->decryptThisUser($user_id);

        return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
       
    }

}
