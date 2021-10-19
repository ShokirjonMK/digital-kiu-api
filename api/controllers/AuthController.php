<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use api\forms\Login;

class AuthController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['permissionCheck']);
        unset($behaviors['authorCheck']);
        return $behaviors;
    }

    public function actionLogin()
    {
        $result = Login::login(new Login(), Yii::$app->request->post());
        if ($result['is_ok']){
            return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);     
        }
    }
    
}
