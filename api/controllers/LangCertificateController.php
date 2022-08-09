<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\LangCertificate;
use Yii;

class LangCertificateController extends ApiActiveController
{
    public $modelClass = 'api\resources\LangCertificate';

    public function actions()
    {
        return [];
    }

    public $table_name = 'lang_certificate';
    public $controller_name = 'LangCertificate';

    public function actionCreate($lang)
    {
        $model = new LangCertificate();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = LangCertificate::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = LangCertificate::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = LangCertificate::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = LangCertificate::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


    public function actionDelete($lang, $id)
    {
        $model = LangCertificate::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        $model->delete();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}