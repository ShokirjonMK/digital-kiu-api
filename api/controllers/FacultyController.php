<?php

namespace api\controllers;

use common\models\model\Faculty;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Translate;
use yii\web\ForbiddenHttpException;

class FacultyController extends ApiController
{
    // public $modelClass = 'api\resources\Faculty';

    public function actions()
    {
        return [];
    }

    public $table_name = 'faculty';
    public $controller_name = 'Faculty';
    
    public function actionIndex($lang)
    {
        $model = new Faculty();
        
        $query = $model->find()
            ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->join("LEFT JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
            // ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['tr.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        // sort
        $query = $this->sort($query);
        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Faculty();
        $translate = new Translate();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        // $this->loadToTranslate($post['name']);

        // return 1;
        $result = Faculty::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name.' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Faculty::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Faculty::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Faculty::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Faculty::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
