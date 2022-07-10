<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\ExamNoStudent;
use common\models\model\ExamStudent;
use common\models\model\Profile;

class ExamStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamQuestionOption';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student';
    public $controller_name = 'Exam Student';

    public function actionCorrect($lang, $key)
    {

        // $rows = (new \yii\db\Query())
        // ->from('user')
        // ->where(['last_name' => 'Smith'])
        // ->limit(10)
        // ->all();

        // $model = new ExamStudent();
        // $i = 0;
        // for ($i = 0; $i <= 4; $i++) {
        // }

        ExamStudent::correct($key);

        return "Success";
    }

    public function actionIndex($lang)
    {
        $model = new ExamStudent();

        $query = $model->find()
            ->andWhere(['exam_student.is_deleted' => 0])
            ->join('INNER JOIN', 'student', 'student.id = exam_student.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->andFilterWhere(['like', 'option', Yii::$app->request->get('q')]);


        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

        if (isRole("teacher")) {
            $query = $query->andWhere([
                'in', 'teacher_access_id', $this->teacher_access()
            ]);
        }

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
        $model = new ExamStudent();
        $post = Yii::$app->request->post();

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['start'])) {
            $post['start'] = strtotime($post['start']);
        }

        if (isset($post['finish'])) {
            $post['finish'] = strtotime($post['finish']);
        }

        $this->load($model, $post);

        $result = ExamStudent::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        if (isRole('teacher')) {
            $model = ExamNoStudent::findOne($id);
        } else {
            $model = ExamStudent::findOne($id);
        }
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole("teacher")) {
            if (is_null($model->teacher_access_id)) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This Exam Student did not given'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
            if ($model->teacherAccess->user_id != current_user_id()) {
                return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }

        $post = Yii::$app->request->post();

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['start'])) {
            $post['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $post['finish'] = strtotime($post['finish']);
        }

        $post['old_file'] = $model->plagiat_file;

        $this->load($model, $post);
        // if (isRole("teacher")) {
        //     $model->status = ExamStudent::STATUS_CHECKED;
        // }
        $result = ExamStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        if (isRole('teacher')) {
            $model = ExamNoStudent::find()
                ->andWhere(['id' => $id, 'is_deleted' => 0])
                ->one();
        } else {
            $model = ExamStudent::find()
                ->andWhere(['id' => $id, 'is_deleted' => 0])
                ->one();
        }

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole("teacher")) {
            if ($model->teacherAccess->user_id != current_user_id()) {
                return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = ExamStudent::deleteMK($model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' succesfully cleared for next attempt.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
