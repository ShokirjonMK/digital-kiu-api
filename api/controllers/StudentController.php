<?php

namespace api\controllers;

use Yii;
use api\resources\StudentUser;
use api\resources\Profile;
use api\resources\User;

use common\models\Student as CommonStudent;
use base\ResponseStatus;
use common\models\model\Student;

class  StudentController extends ApiActiveController
{

    public $modelClass = 'api\resources\Student';

    public function actions()
    {
        return [];
    }
    public function actionIndex($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->andWhere(['status' => 1, 'is_deleted' => 0])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new User();
        $profile = new Profile();
        $student = new Student();
        $post = Yii::$app->request->post();

        $users = Student::find()->count();
        $count = $users + 10000;
        $post['username'] = 'tsul-'. $count;
        $post['email'] = 'tsul-'. $count.'@tsul.uz';

        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::createItem($model, $profile, $student, $post);
        $data = [];
        $data['user'] = $model;
        $data['profile'] = $profile;
        $data['student'] = $student;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully created.'), $data, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Student::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Student::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Student::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Student::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = Student::findOne($id);

        if ($result) {
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('Student succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
