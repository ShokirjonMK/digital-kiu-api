<?php

namespace api\controllers;

use common\models\model\EduSemestrSubject;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Student;

class EduSemestrSubjectController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestrSubject';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new EduSemestrSubject();

        $student = Student::findOne(['user_id' => Yii::$app->user->identity->id]);
        if (isset($student)) {
            $eduSemesterIds = EduSemestr::find()
                ->select('id')
                ->where(['edu_plan_id' => $student->edu_plan_id]);

            $query = $model->find()
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['in', 'edu_semestr_id', $eduSemesterIds]);
        } else {
            $query = $model->find()
                ->andWhere(['is_deleted' => 0]);
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
        $model = new EduSemestrSubject();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestrSubject::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Edu Semestr Subject successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduSemestrSubject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestrSubject::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Edu Semestr Subject successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduSemestrSubject::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = EduSemestrSubject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model

        if ($model) {
            $result = EduSemestrSubject::deleteItem($model);

            if (!is_array($result)) {
                return $this->response(1, _e('EduSemestrSubject succesfully removed.'), null, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
