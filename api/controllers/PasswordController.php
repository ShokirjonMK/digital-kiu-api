<?php

namespace api\controllers;

use Yii;
use api\resources\User;
use api\resources\Password;
use base\ResponseStatus;
use common\models\model\AuthChild;
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
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
    }

    public function actionView($lang, $id)
    {
        $user_id = $id;
        if (current_user_id() != $user_id) {
            $isChild =
                AuthChild::find()
                ->where(['in', 'child', current_user_roles_array($user_id)])
                ->andWhere(['in', 'parent', current_user_roles_array()])
                ->all();
            if (!$isChild) return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $data = new Password();
        $data = $data->decryptThisUser($user_id);

        return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    }
}
