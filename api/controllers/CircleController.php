<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\Circle;
use common\models\model\CircleSchedule;
use Yii;

class CircleController extends ApiActiveController
{
    public $modelClass = 'api\resources\Circle';

    public function actions()
    {
        return [];
    }

    public $controller_name = 'Circle';

    public function actionIndex($lang)
    {
        $model = new Circle();

        $query = $model
            ->find()
            ->leftJoin('translate tr', "tr.model_id = {$model->tableName()}.id and tr.table_name = '{$model->tableName()}'")
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);


        $query->andWhere([$model->tableName() . '.is_deleted' => Yii::$app->request->get('is_deleted', 0)]);

        $query->groupBy($model->tableName() . '.id');

        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Circle();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Circle::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        if ($id == 0) {
            $post = Yii::$app->request->post();
            $finishStatus = $post['finish_status'] ?? null;

            if ($finishStatus === null) {
                return $this->response(0, _e('Finish status is required.'), null, null, ResponseStatus::BAD_REQUEST);
            }

            $updated = Circle::updateAll(['finish_status' => $finishStatus]);
            if ($updated) {
                return $this->response(1, _e('Success.'), null, null, ResponseStatus::OK);
            }
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        // id = 0

        $model = Circle::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Circle::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Circle::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Circle::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    // /api/circles/{id}/schedules [GET, POST]
    public function actionSchedules($lang, $id)
    {
        if (Yii::$app->request->isGet) {
            $query = CircleSchedule::find()->where(['circle_id' => $id, 'is_deleted' => 0]);
            $query = $this->filterAll($query, new CircleSchedule());
            $query = $this->sort($query);
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (Yii::$app->request->isPost) {
            $model = new CircleSchedule();
            $post = Yii::$app->request->post();
            $post['circle_id'] = $id;
            $this->load($model, $post);
            $result = CircleSchedule::createItem($model, $post);
            if (!is_array($result)) {
                return $this->response(1, _e('Schedule successfully created.'), $model, null, ResponseStatus::CREATED);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        return $this->response(0, _e('Bad request.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
