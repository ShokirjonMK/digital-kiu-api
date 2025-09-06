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
            'certificate_file' => $model->certificate_file,
            'certificate_date' => $model->certificate_date,
            'student_full_name' => $studentFullName,
            'certificate_url' => Yii::getAlias('@web') . '/storage' . $model->certificate_file
        ];

        return $this->response(1, _e('Certificate found.'), $certificateData, null, ResponseStatus::OK);
    }
}