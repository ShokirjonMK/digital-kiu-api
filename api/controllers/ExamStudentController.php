<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\ExamNoStudent;
use common\models\model\ExamStudent;
use common\models\model\TeacherAccess;

class ExamStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamQuestionOption';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student';
    public $controller_name = 'Exam Student';

    public function actionIndex($lang)
    {
        $model = new ExamStudent();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andFilterWhere(['like', 'option', Yii::$app->request->get('q')]);

        if (isRole("teacher")) {
            $query = $query->andWhere([
                'in', 'teacher_access_id', $this->teacher_access()
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
            if ($model->teacherAccess->user_id != current_user_id()) {
                return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }

        $post = Yii::$app->request->post();
        $post['old_file'] = $model->plagiat_file;

        $this->load($model, $post);
        $result = ExamStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
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

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
