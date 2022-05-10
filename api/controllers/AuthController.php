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
        $post = Yii::$app->request->post();
        if (isset($post['is_main'])) {
            if ($post['is_main'] == 1) {
                $result = Login::loginMain(new Login(), $post);
                if ($result['is_ok']) {
                    if (empty($result['data']['role'])) {
                        Login::logout();
                        // return $result['data']['role'];
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                    return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
                }
            } elseif ($post['is_main'] == 0) {
                $result = Login::loginStd(new Login(), $post);
                if ($result['is_ok']) {
                    if (empty($result['data']['role'])) {
                        Login::logout();
                        // return $result['data']['role'];
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                    return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
                }
            }
        } else {
            /* olib tashlash sharti bilan */
            $result = Login::login(new Login(), $post);
            if ($result['is_ok']) {
                return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
            }
            /* olib tashlash sharti bilan */

            return $this->response(0, _e('Something went wrong!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $result = Login::login(new Login(), $post);
        if ($result['is_ok']) {
            return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
        }
    }
}
