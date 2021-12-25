<?php

namespace api\controllers;

use common\models\model\EduSemestr;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Student;

class EduSemestrController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestr';

    public function actions()
    {
        return [];
    }

    
    public function actionIndex($lang)
    {
        $model = new EduSemestr();

        $student = Student::findOne(['user_id' => Yii::$app->user->identity->id]);

        if (isset($student)) {
            $query = $model->find()
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['edu_plan_id' => $student->edu_plan_id])
                ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);
        } else {
            $query = $model->find()
                ->andWhere(['is_deleted' => 0])
                ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);
        }

        //filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);

        $model = new EduSemestr();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestr::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('EduSemestr successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduSemestr::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestr::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('EduSemestr successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduSemestr::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
        $model = EduSemestr::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = EduSemestr::findOne($id);

        if ($result) {
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('Edu Semestr succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
