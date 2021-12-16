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
            ->andWhere(['is_deleted' => 0])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);

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
        $post = Yii::$app->request->post();

        $post['role'] = 'student';
        $model = new User();
        $profile = new Profile();
        $student = new Student();

        $users = Student::find()->count();
        $count = $users + 10001;
        $post['username'] = 'tsul-std-' . $count;
        $post['email'] = 'tsul-std' . $count . '@tsul.uz';

        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::createItem($model, $profile, $student, $post);
        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully created.'), $data, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();
        $post['role'] = 'student';
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model = User::findOne(['id', $student->user_id]);
        $profile = Profile::findOne(['user_id', $student->user_id]);

        
        $this->load($student, $post);
        $result = StudentUser::updateItem($model, $profile, $student, $post);

        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $data, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Student::findOne(['id'=>$id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e('Student succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
