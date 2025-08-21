<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\CircleAttendance;
use common\models\model\CircleSchedule;
use common\models\model\CircleStudent;
use Yii;

class CircleScheduleController extends ApiActiveController
{
    public $modelClass = 'api\resources\CircleSchedule';

    public function actions()
    {
        return [];
    }

    public $controller_name = 'Schedule';

    public function actionIndex($lang)
    {
        $model = new CircleSchedule();

        $query = $model
            ->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0]);

        if (isRole('student')) {
            $student = $this->student();
            $building_id = $student->direction->building_id ?? null;
            if ($building_id !== null) {
                $query->andWhere(['building_id' => $building_id]);
            }
        }

        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new CircleSchedule();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = CircleSchedule::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = CircleSchedule::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = CircleSchedule::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = CircleSchedule::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = CircleSchedule::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    // /api/schedules/{id}/enroll [POST]
    public function actionEnroll($lang, $id)
    {
        $post = Yii::$app->request->post();
        $model = new CircleStudent();
        $model->circle_schedule_id = $id;
        $model->student_user_id = $post['student_user_id'] ?? current_user_id();
        if (!isset($post['student_id'])) {
            return $this->response(0, _e('student_id is required.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $model->student_id = (int) $post['student_id'];

        $result = CircleStudent::createItem($model);
        if (!is_array($result)) {
            return $this->response(1, _e('Enrollment successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    // /api/schedules/{id}/students [GET]
    public function actionStudents($lang, $id)
    {
        $query = CircleStudent::find()->where(['circle_schedule_id' => $id, 'is_deleted' => 0]);
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    // /api/schedules/{id}/attendance [POST]
    public function actionAttendance($lang, $id)
    {
        if (Yii::$app->request->isGet) {
            $query = CircleAttendance::find()->where(['circle_schedule_id' => $id]);
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (Yii::$app->request->isPost) {
            $schedule = CircleSchedule::findOne($id);
            if (!$schedule) {
                return $this->response(0, _e('Schedule not found.'), null, null, ResponseStatus::NOT_FOUND);
            }

            if ($schedule->teacher_user_id != current_user_id()) {
                return $this->response(0, _e('Only schedule teacher can set attendance.'), null, null, ResponseStatus::FORBIDDEN);
            }

            $post = Yii::$app->request->post();
            if (!isset($post['circle_student_id'])) {
                return $this->response(0, _e('circle_student_id is required.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }

            $circleStudent = CircleStudent::findOne((int) $post['circle_student_id']);
            if (!$circleStudent || $circleStudent->circle_schedule_id != $id) {
                return $this->response(0, _e('Invalid circle_student_id.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }

            $attendance = new CircleAttendance();
            $attendance->circle_student_id = $circleStudent->id;
            $attendance->circle_schedule_id = $id;
            $attendance->circle_id = $schedule->circle_id;
            $attendance->student_id = $circleStudent->student_id;
            $attendance->teacher_user_id = $schedule->teacher_user_id;  // per comment: schedule teacher
            $attendance->date = $post['date'] ?? time();
            $attendance->reason = $post['reason'] ?? 0;
            $attendance->status = $post['status'] ?? 1;

            if ($attendance->validate() && $attendance->save()) {
                return $this->response(1, _e('Attendance saved.'), $attendance, null, ResponseStatus::CREATED);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $attendance->getErrorSummary(true), ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        return $this->response(0, _e('Bad request.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
