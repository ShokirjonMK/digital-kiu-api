<?php

namespace api\controllers;

use common\models\model\ExamStudentAnswer;
use Yii;
use base\ResponseStatus;
use common\models\model\Exam;
use common\models\model\ExamStudent;
use common\models\model\ExamStudentAnswerForTeacher;
use common\models\model\Student;

class ExamCheckingController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamChecking';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student_answer';
    public $controller_name = 'Exam Checking';


    public function actionIndex($lang)
    {
        $errors = [];

        $model = new ExamStudentAnswerForTeacher();

        $query = $model->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0]);

        if (!isRole("admin")) {
            $examStudentIds = ExamStudent::find()
                ->where(['in', 'teacher_access_id', $this->teacher_access()])
                ->select('id');
            $query = $query
                ->andWhere(['in', $model->tableName() . '.exam_student_id', $examStudentIds]);
        }


        $exam_student_id = Yii::$app->request->get('exam_student_id');

        if ($exam_student_id) {
            $query = $query->andFilterWhere([$model->tableName() . '.exam_student_id' => $exam_student_id]);
        } else {
            $errors[] = ['exam_student_id' => _e('Required')];
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        $model = new ExamStudentAnswer();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = ExamStudentAnswer::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $errors = [];
        $model = ExamStudentAnswerForTeacher::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        // if (isRole("teacher")) {
        if (!is_null($model->examStudent)) {
            if (!is_null($model->examStudent->teacherAccess)) {

                if ($model->examStudent->exam->status == Exam::STATUS_ANNOUNCED) {
                    return $this->response(0, _e('You can not check.'), null, null, ResponseStatus::FORBIDDEN);
                }

                if ($model->examStudent->exam->appeal_start > 0) {
                    return $this->response(0, _e('You can not check.'), null, null, ResponseStatus::FORBIDDEN);
                }


                if ($model->examStudent->teacherAccess->user_id != current_user_id()) {
                    return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
                } else {
                    $post['teacher_access_id'] = $model->examStudent->teacher_access_id;
                }
                $data = [];
                if (isset($post['teacher_conclusion'])) {

                    if (($post['teacher_conclusion'][0] == "'") && ($post['teacher_conclusion'][strlen($post['teacher_conclusion']) - 1] == "'")) {
                        $post['teacher_conclusion'] =  substr($post['teacher_conclusion'], 1, -1);
                    }

                    $data['teacher_conclusion'] = $post['teacher_conclusion'];
                }
                if (isset($post['ball'])) {
                    $data['ball'] = $post['ball'];
                }
                if (isset($post['subQuestionAnswersChecking'])) {
                    $data['subQuestionAnswersChecking'] = $post['subQuestionAnswersChecking'];
                }
            } else {
                $errors[] = _e("teacherAccess");
                return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            $errors[] = _e("examStudent");
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $this->load($model, $data);
        $result = ExamStudentAnswer::updateItemTeacher($model, $data);
        // } else {
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        // }

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamStudentAnswer::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
