<?php

namespace api\controllers;
use base\ResponseStatus;
use common\models\model\ExamControl;
use Yii;
use yii\rest\ActiveController;

class ExamControlController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControl';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_control';
    public $controller_name = 'ExamControl';

    public function actionIndex($lang)
    {
        $model = new ExamControl();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);
        if (!isRole('admin')) {
            $query = $query->andWhere(['in', 'role', current_user_roles_array()]);
        }
        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

    }

    public function actionCreate($lang)
    {
        $model = new ExamControl();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ExamControl::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamControl::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ExamControl::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamControl::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControl::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        $model->delete();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


}