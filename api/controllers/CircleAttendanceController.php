<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\CircleAttendance;
use Yii;

class CircleAttendanceController extends ApiActiveController
{
    public $modelClass = 'api\resources\CircleAttendance';

    public function actions()
    {
        return [];
    }

    public $controller_name = 'Attendance';

    public function actionIndex($lang)
    {
        $model = new CircleAttendance();

        $query = $model->find();

        $query->leftJoin("circle_schedule", "circle_schedule.id = " . $model->tableName() . ".circle_schedule_id");

        if (isRole('teacher')) {
            $query->andWhere(['circle_schedule.teacher_user_id' => current_user_id()]);
        }

        if (isRole('student')) {
            $student_id = $this->student();
            if ($student_id !== null) {
                $query->andWhere(['student_id' => $student_id]);
            }
        }

        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new CircleAttendance();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        if (isset($post['circle_student_ids'])) {
            $result = CircleAttendance::createItems($post);
            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            $result = CircleAttendance::createItem($model, $post);
            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = CircleAttendance::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $result = CircleAttendance::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = CircleAttendance::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('teacher') && $model->circleSchedule->teacher_user_id !== current_user_id()) {
            return $this->response(0, _e('You are not authorized to view.'), null, null, ResponseStatus::FORBIDDEN);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = CircleAttendance::find()
            ->andWhere(['id' => $id])
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('teacher') && $model->circleSchedule->teacher_user_id !== current_user_id()) {
            return $this->response(0, _e('You are not authorized to view.'), null, null, ResponseStatus::FORBIDDEN);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
