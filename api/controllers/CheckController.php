<?php

namespace api\controllers;

use common\models\model\CircleStudent;
use base\ResponseStatus;
use Yii;
use yii\rest\ActiveController;

class CheckController extends ActiveController
{

    use ApiOpen;

    public $modelClass = 'api\resources\CircleStudent';

    public function actions()
    {
        return [];
    }

    /**
     * Open controller to check certificate by CircleStudent ID
     * Returns certificate file, date, and student full name
     */
    public function actionCertificate($id)
    {
        $model = CircleStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Certificate not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // Check if certificate exists
        if (empty($model->certificate_file) || $model->certificate_status != 1) {
            return $this->response(0, _e('Certificate not generated yet.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // Get student full name
        $studentFullName = '';
        if ($model->student && $model->student->profile) {
            $studentFullName = $model->student->fullName;
        }

        $certificateData = [
            'id' => $model->id,
            'circle' => $model->circle->translate->name ?? '',
            'circle_schedule' => [
                'start_date' => $model->circleSchedule->start_date ?? '',
                'end_date' => $model->circleSchedule->end_date ?? '',
                'start_time' => $model->circleSchedule->start_time ?? '',
                'end_time' => $model->circleSchedule->end_time ?? '',
                'week' => $model->circleSchedule->week->translate->name ?? '',
                'building' => $model->circleSchedule->building->translate->name ?? '',
                'teacher' => ['first_name' => $model->circleSchedule->teacher->profile->first_name ?? '', 'last_name' => $model->circleSchedule->teacher->profile->last_name] ?? '',
                'edu_year' => $model->circleSchedule->eduYear->translate->name ?? '',
                'semestr_type' => $model->circleSchedule->semestr_type ?? '',

            ],
            'certificate_file' => $model->certificate_file ?? '',
            'certificate_date' => $model->certificate_date ?? '',
            'student' => ['first_name' => $model->student->profile->first_name ?? '', 'last_name' => $model->student->profile->last_name ?? '', 'middle_name' => $model->student->profile->middle_name],

        ];

        return $this->response(1, _e('Certificate found.'), $certificateData, null, ResponseStatus::OK);
    }
}
