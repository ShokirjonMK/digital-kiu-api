<?php

namespace api\controllers;

use common\models\model\HostelApp;
use Yii;
use base\ResponseStatus;

class HostelAppController extends ApiActiveController
{
    public $modelClass = 'api\resources\HostelApp';

    public function actions()
    {
        return [];
    }

    public $table_name = 'hostel_app';
    public $controller_name = 'HostelApp';

    public function actionIndex($lang)
    {
        $model = new HostelApp();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);


        if (isRole("student")) {
            $query = $query->andWhere([
                'student_id' => $this->student()
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new HostelApp();
        $post = Yii::$app->request->post();
        $post['user_id'] = current_user_id();

        if (!isRole("student")) {
            return $this->response(0, _e('This action is only for students.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $student = $this->student(2);
        if (!$student) {
            return $this->response(0, _e('Student not found.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        // return $student;
        $post['student_id'] = $student->id;
        $post['faculty_id'] = $student->faculty_id;


        $this->load($model, $post);
        $result = HostelApp::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = HostelApp::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $result = HostelApp::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = HostelApp::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = HostelApp::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->delete();
            // $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
