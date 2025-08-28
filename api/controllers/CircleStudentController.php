<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\CircleStudent;

use Yii;
use yii\web\NotFoundHttpException;

class CircleStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\CircleStudent';

    public function actions()
    {
        return [];
    }

    public $controller_name = 'Circle Student';

    public function actionIndex($lang)
    {
        $model = new CircleStudent();

        $query = $model
            ->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0]);

        if (isRole('student')) {
            $query->andWhere(['student_user_id' => current_user_id()]);
        }

        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new CircleStudent();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = CircleStudent::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

        $model = CircleStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = CircleStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = CircleStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole('student') && $model->student_user_id !== current_user_id()) {
            return $this->response(0, _e('You are not authorized to view.'), null, null, ResponseStatus::FORBIDDEN);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = CircleStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            if ($model->update()) {
                $schedule = $model->circleSchedule;
                $schedule->student_count = CircleStudent::find()
                    ->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])
                    ->count();
                $schedule->save(false);
                return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
            }
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    /**
     * Generate certificate PDF for a circle student and save path/date
     */
    public function actionCertificate($id)
    {
        $model = CircleStudent::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("CircleStudent not found.");
        }
        // $result = CircleStudent::generateCertificateTest($model);
        $reject = Yii::$app->request->get('reject');
        $result = $reject ? CircleStudent::rejectCertificate($model) : CircleStudent::generateCertificateTest($model);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' certificate generated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
