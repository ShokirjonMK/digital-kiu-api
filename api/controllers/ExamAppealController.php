<?php

namespace api\controllers;

use common\models\model\ExamAppeal;
use Yii;
use base\ResponseStatus;
use common\models\model\Profile;
use common\models\model\Student;

class ExamAppealController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamAppeal';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_appeal';
    public $controller_name = 'ExamAppeal';

    public function actionIndex($lang)
    {
        $model = new ExamAppeal();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andFilterWhere(['like', $this->table_name . 'appeal_text', Yii::$app->request->get('q')]);


        $query->join('INNER JOIN', 'student', 'student.id = exam_appeal.student_id')
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
                'created_by' => current_user_id()
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
        $model = new ExamAppeal();

        // $errors['time'] = time();
        // // $errors['appeal_finish'] = $model->examStudent->exam->appeal_finish;

        // return simplify_errors($errors);

        if (!isRole('student') && !isRole('admin')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        $student = Student::findOne(['user_id' => current_user_id()]);
        if (!$student) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, _e('Student not found'), ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();

        $post['faculty_id'] = $student->faculty_id;
        $post['student'] = $student;

        $this->load($model, $post);
        $result = ExamAppeal::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamAppeal::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();


        if (isRole('teacher')) {
            if ($model->teacherAccess->user_id == current_user_id()) {
                $result = ExamAppeal::teacherUpdateItem($model, $post);
                if (!is_array($result)) {
                    return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            }
        }

        // $data = [];

        // if (isRole('teacher')) {

        //     if ($model->teacherAccess->user_id !=  current_user_id()) {
        //         return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
        //     }

        //     $examStudentAnswer
        //     if (isset($post['teacher_conclusion'])) {
        //         $data['teacher_conclusion'] = $post['teacher_conclusion'];
        //     }


        //     if ($model->examStudent->teacherAccess->user_id != current_user_id()) {
        //         return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
        //     } else {
        //         $post['teacher_access_id'] = $model->examStudent->teacher_access_id;
        //     }
        //     $data = [];
        //     if (isset($post['teacher_conclusion'])) {
        //         $data['teacher_conclusion'] = $post['teacher_conclusion'];
        //     }
        //     if (isset($post['ball'])) {
        //         $data['ball'] = $post['ball'];
        //     }
        //     if (isset($post['subQuestionAnswersChecking'])) {
        //         $data['subQuestionAnswersChecking'] = $post['subQuestionAnswersChecking'];
        //     }

        //     $this->load($model, $data);
        //     $result = ExamStudentAnswer::updateItemTeacher($model, $data);

        //     if (!is_array($result)) {
        //         return $this->response(1, _e($this->controller_name . ' successfully saved.'), $model, null, ResponseStatus::OK);
        //     } else {
        //         return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        //     }
        // }


        // sassdlasl;dkasl;d
        /* if (!isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        $student = Student::findOne(['user_id' => current_user_id()]);
        if (!$student) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, _e('Student not found'), ResponseStatus::UPROCESSABLE_ENTITY);
        } */


        // $this->load($model, $post);

        $this->load($model, $post);
        $result = ExamAppeal::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamAppeal::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamAppeal::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // $model->is_deleted = 1;
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
