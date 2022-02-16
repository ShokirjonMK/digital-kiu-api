<?php

namespace api\controllers;

use common\models\model\ExamStudentAnswer;
use Yii;
use base\ResponseStatus;

class ExamStudentAnswerController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamStudentAnswer';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student_answer';
    public $controller_name = 'Exam Student Answer';

    public function actionGetQuestion($lang)
    {
        $data = [];
        $model = new ExamStudentAnswer();
        $post = Yii::$app->request->post();

        $ExamStudentAnswer = new ExamStudentAnswer();
        $ExamStudentAnswer->exam_id = 1;
        $ExamStudentAnswer->question_id = 1;
        $ExamStudentAnswer->student_id = 15;
        $ExamStudentAnswer->type = 1;
        $ExamStudentAnswer->attempt = 1;
        $ExamStudentAnswer->status = ExamStudentAnswer::STATUS_NEW;
        /* $errors['model'] = $ExamStudentAnswer;
                                    $errors['ques'] = $questionAll;
                                    $transaction->rollBack();
                                    return simplify_errors($errors); */
        $ExamStudentAnswer->save();

        return $ExamStudentAnswer;
        
        $result = ExamStudentAnswer::randomQuestions($post);

        if (isset($result['questions'])) {
            return $this->response(1, _e('Questions and times'), $result, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    }


    public function actionIndex($lang)
    {
        $model = new ExamStudentAnswer();

        $query = $model->find()
            ->andWhere(['.is_deleted' => 0]);

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
        $model = ExamStudentAnswer::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = ExamStudentAnswer::updateItem($model, $post);
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

    public function actionDelete($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        $model = ExamStudentAnswer::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
