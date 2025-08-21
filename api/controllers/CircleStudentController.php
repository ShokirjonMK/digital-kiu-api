<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\CircleStudent;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

use Mpdf\Mpdf;
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

        $circleName  = "To'garak nomi";
        $studentName = "Ism falimiya talaba"; // student modelda full_name bo‘lsa
        $certDate    = date('Y-m-d');
        $filePath    = Yii::getAlias('@webroot/uploads/certificates/certificate_' . $id . '.pdf');
        $fileUrl     = Yii::getAlias('@web/uploads/certificates/certificate_' . $id . '.pdf');

        // ⚡️ QR code generatsiya qilish
        $qrCode = QrCode::create("https://domen.uz/circle-student/{$id}")
            ->setSize(100);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // QR ni base64 qilib olish
        $qrBase64 = base64_encode($result->getString());


        // ⚡️ mPDF instance
        $mpdf = new Mpdf();

        // HTML template
        $html = "
        <div style='position: relative; width:100%; height:100%; font-family: sans-serif;'>
            <!-- Top-left image -->
            <div style='position: absolute; top: 10px; left: 10px;'>
                <img src='" . Yii::getAlias('@web/images/logo.png') . "' width='100'>
            </div>

            <!-- Centered content -->
            <div style='text-align: center; margin-top: 150px;'>
                <h1 style='font-size: 32px; margin-bottom: 20px;'>{$circleName}</h1>
                <h2 style='font-size: 24px;'>{$studentName}</h2>
                <p style='margin-top: 30px;'>Certificate Date: {$certDate}</p>
            </div>

            <!-- Bottom-right QR -->
            <div style='position: absolute; bottom: 20px; right: 20px;'>
                <img src='data:image/png;base64,<?= $qrBase64 ?>' width='100'>

            </div>
        </div>
        ";

        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // Modelni update qilish
        $model->certificate_file = $fileUrl;
        $model->certificate_date = $certDate;
        $model->save(false);

        return $fileUrl;
    }


    public function actionCertificate1($lang, $id)
    {
        $model = CircleStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('student') && $model->student_user_id !== current_user_id()) {
            return $this->response(0, _e('You are not authorized to generate this certificate.'), null, null, ResponseStatus::FORBIDDEN);
        }

        if (!class_exists('Mpdf\\Mpdf')) {
            return $this->response(0, _e('PDF library not installed. Please require mpdf/mpdf.'), null, ['dependency' => 'mpdf/mpdf'], ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $circle = $model->circle; // expects relation
        $student = $model->student; // expects relation

        $circleName = ($circle && $circle->translate) ? ($circle->translate->name ?? '') : '';
        $studentName = '';
        if ($student) {
            $studentName = $student->fullName
                ?? trim(($student->last_name ?? '') . ' ' . ($student->first_name ?? '') . ' ' . ($student->middle_name ?? ''));
        }

        $qrUrl = 'https://domen.uz/circle-student/' . $model->id;

        // $logoUrl = Yii::$app->params['certificateLogo'] ?? null; // optional logo path/url

        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            .page { padding: 40px; position: relative; min-height: 100%; }
            .top-left { position: absolute; left: 40px; top: 40px; }
            .title { text-align: center; margin-top: 120px; font-size: 28px; font-weight: bold; }
            .student { text-align: center; margin-top: 20px; font-size: 20px; }
            .bottom-right { position: absolute; right: 40px; bottom: 40px; text-align: right; }
        </style></head><body><div class="page">';

        // if (!empty($logoUrl)) {
        //     $html .= '<div class="top-left"><img src="' . htmlspecialchars($logoUrl) . '" height="60"/></div>';
        // }

        $html .= '<div class="title">' . htmlspecialchars($circleName) . '</div>';
        $html .= '<div class="student">' . htmlspecialchars($studentName) . '</div>';
        // $html .= '<div class="bottom-right"><img src="' . htmlspecialchars($qrImg) . '" height="120"/><div style="font-size:10px">' . htmlspecialchars($qrUrl) . '</div></div>';
        $html .= '</div></body></html>';

        $dir = rtrim(STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'certificate' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        $fileName = 'circle_cert_' . $model->id . '_' . time() . '.pdf';
        $absPath = $dir . $fileName;

        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir()]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($absPath, \Mpdf\Output\Destination::FILE);

        $publicPath = 'storage/uploads/certificate/' . $fileName;
        $model->certificate_file = $publicPath;
        $model->certificate_date = time();
        $model->certificate_status = 1;
        $model->save(false);

        return $this->response(1, _e('Certificate generated.'), [
            'file' => $publicPath,
            'url' => (Yii::$app->params['domain_name'] ?? '') . '/' . $publicPath,
        ], null, ResponseStatus::OK);
    }
}
