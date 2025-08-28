<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use yii\behaviors\TimestampBehavior;
use common\models\model\Student;
use common\models\model\CircleSchedule;
use common\models\model\EduYear;

use Mpdf\Mpdf;

use Yii;

class CircleStudent extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public static function tableName()
    {
        return 'circle_student';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const MAX_SCHEDULES_PER_SEMESTER = 2;

    public function rules()
    {
        return [
            [['circle_schedule_id'], 'required'],
            [['circle_id', 'edu_year_id', 'semestr_type', 'circle_schedule_id', 'student_user_id', 'student_id', 'is_finished', 'abs_status', 'certificate_status', 'certificate_date', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['certificate_file'], 'string', 'max' => 255],
            [['circle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Circle::className(), 'targetAttribute' => ['circle_id' => 'id']],
            [['circle_schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => CircleSchedule::className(), 'targetAttribute' => ['circle_schedule_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],

            ['student_id', 'unique', 'targetAttribute' => ['student_id', 'circle_id', 'is_deleted'], 'message' => _e('Student already enrolled to this circle in current semester.')],
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'circle_id',
            'circle_schedule_id',
            'student_user_id',
            'student_id',
            'is_finished',
            'abs_status',
            'edu_year_id',
            'semestr_type',
            'certificate_status',
            'status',
            'created_at',
            'updated_at',
            'is_deleted',
            'created_by',
            'updated_by',
        ];

        // Only include 'certificate_file' if certificate_status == 1
        if ($this->certificate_status == 1) {
            $fields[] = 'certificate_file';
            $fields[] = 'certificate_date';
        }

        return $fields;
    }

    public function extraFields()
    {
        return [
            'circle',
            'circleSchedule',
            'student',
            'teacher',
            'attendances',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];
    }

    public function getCircle()
    {
        return $this->hasOne(Circle::className(), ['id' => 'circle_id']);
    }

    public function getCircleSchedule()
    {
        return $this->hasOne(CircleSchedule::className(), ['id' => 'circle_schedule_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getTeacher()
    {
        return $this->circleSchedule->teacher;
    }

    public function getAttendances()
    {
        return $this->hasMany(CircleAttendance::class, ['circle_student_id' => 'id']);
    }

    public function getAbsCount()
    {
        return $this->getAttendances()
            ->andWhere(['status' => 1, 'reason' => 0])
            ->count();
    }

    public function getAbsCountAll()
    {
        return $this->getAttendances()->count();
    }

    public function getMyAttendance()
    {
        return $this->getAttendances()
            ->andWhere([
                'status' => 1,
                'reason' => 0,
                'student_id' => current_user_id()
            ])
            ->count();
    }


    /**
     * Talabani Circle (to‘garak) schedule ga yozish
     *
     * @param CircleEnrollment $model  // yoziladigan enrollment modeli
     * @param array $post              // request'dan keladigan ma'lumotlar
     * @return bool|array              // true yoki xatolar massivi
     */
    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        try {
            // 👤 Agar foydalanuvchi "student" bo'lsa, student_id avtomatik olinadi
            if (isRole('student')) {
                $model->student_id = self::student();
            } else {
                // Admin yoki boshqa rol orqali kelganda student_id majburiy
                if (empty($post['student_id'])) {
                    $errors[] = _e('Student id is required.');
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                $model->student_id = $post['student_id'];
            }

            // 🔍 Model validatsiyasi
            if (!$model->validate()) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // Student user_id ni avtomatik olish
            $model->student_user_id = $model->student->user_id;
            $model->circle_id = $model->circleSchedule->circle_id;

            $schedule = $model->circleSchedule;

            // ✅ 0) Course-based selection window check
            $studentModel = $model->student; // relation
            if ($studentModel && $studentModel->course_id) {
                $course = Course::findOne($studentModel->course_id);
                if ($course) {
                    $now = time();
                    // Determine term window by schedule semestr_type: 1=kuz (fall), 2=bahor (spring)
                    $useFall = ((int)$schedule->semestr_type === 1);

                    $fromStr = $useFall ? ($course->circle_kuz_from ?? '') : ($course->circle_bahor_from ?? '');
                    $toStr   = $useFall ? ($course->circle_kuz_to ?? '')   : ($course->circle_bahor_to ?? '');

                    if ($fromStr && $toStr) {
                        // Compose with current year (or schedule edu_year_id if that is a year value)
                        $year = (int)date('Y');
                        $fromTs = strtotime($year . '-' . $fromStr);
                        $toTs   = strtotime($year . '-' . $toStr);

                        // If window crosses year boundary (e.g., Dec -> Jan), adjust
                        if ($toTs !== false && $fromTs !== false && $toTs < $fromTs) {
                            // assume to is next year
                            $toTs = strtotime(($year + 1) . '-' . $toStr);
                        }

                        if ($fromTs === false || $toTs === false) {
                            $errors[] = _e('Course selection window has invalid format. Expected mm-dd HH:ii:ss');
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }

                        if (!($now >= $fromTs && $now <= $toTs)) {
                            $errors[] = _e('Circle selection is closed for your course at this time.');
                            $transaction->rollBack();
                            return simplify_errors($errors);
                        }
                    }
                }
            }

            // ✅ 1) Max student limit check (admin qo‘lda oshirishi mumkin)
            $currentCount = self::find()
                ->where(['circle_schedule_id' => $model->circle_schedule_id, 'is_deleted' => 0])
                ->count();

            if ($currentCount >= (int) $schedule->max_student_count) {
                $errors[] = _e('Schedule capacity reached');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // ✅ 2) Bir xil Circle ni qayta tanlashni bloklash
            $existsSameCircle = self::find()
                ->alias('cs')
                ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                ->where([
                    'cs.student_id'   => $model->student_id,
                    'cs.is_deleted'   => 0,
                    'sch.circle_id'   => $schedule->circle_id,
                ])
                ->exists();

            if ($existsSameCircle) {
                $errors[] = _e('You already enrolled to this circle');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // ✅ 3) Talabaning bir semestrda maksimal MAX_SCHEDULES_PER_SEMESTER ta schedule limiti
            $countThisSemester = self::find()
                ->alias('cs')
                ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                ->where([
                    'cs.student_id'   => $model->student_id,
                    'cs.is_deleted'   => 0,
                    'sch.edu_year_id' => $schedule->edu_year_id
                ])
                ->count();

            if ($countThisSemester >= self::MAX_SCHEDULES_PER_SEMESTER) {
                $errors[] = _e('You cannot enroll more than') . ' ' .
                    self::MAX_SCHEDULES_PER_SEMESTER . ' ' .
                    _e('schedules in a semester');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // 🔄 Circle_id ni schedule'dan avtomatik olish
            $model->circle_id = $schedule->circle_id;

            // Yana validatsiya (avvalgi xatoliklar bartaraf etilgan bo‘lsa)
            if (!$model->validate()) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // 📝 Saqlash
            if ($model->save()) {
                // Schedule ichidagi student_count ni yangilash
                $schedule->student_count = self::find()
                    ->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])
                    ->count();

                $schedule->save(false);

                $transaction->commit();
                return true;
            } else {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } catch (\Exception $e) {
            // ❌ Exception bo‘lsa transaction rollback qilinadi
            $transaction->rollBack();
            $errors[] = $e->getMessage();
            return simplify_errors($errors);
        }
    }

    public static function createItemOld($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $t = false;
        if (isRole('student')) {
            $model->student_id = self::student();
        } else {
            if (!$post['student_id']) {
                $errors[] = _e('Student id is required.');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            $model->student_id = $post['student_id'];
            $t = true;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_user_id = $model->student->user_id;
        $model->circle_id = $model->circleSchedule->circle_id;



        $schedule = $model->circleSchedule;

        // per migration: max_student_count limit admin can add more students to the schedule
        if ($t) {
            $currentCount = self::find()->where(['circle_schedule_id' => $model->circle_schedule_id, 'is_deleted' => 0])->count();
            if ($currentCount >= (int) $schedule->max_student_count) {
                $errors[] = _e('Schedule capacity reached');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        // cannot reselect same circle in same semester and year
        $existsSameCircle = self::find()
            ->alias('cs')
            ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
            ->where([
                'cs.student_id' => $model->student_id,
                'cs.is_deleted' => 0,
                'sch.circle_id' => $schedule->circle_id,
                'sch.edu_year_id' => $schedule->edu_year_id
            ])
            ->exists();

        if ($existsSameCircle) {
            $errors[] = _e('You already enrolled to this circle');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // max 2 schedules per semester
        $countThisSemester = self::find()
            ->alias('cs')
            ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
            ->where([
                'cs.student_id' => $model->student_id,
                'cs.is_deleted' => 0,
                'sch.edu_year_id' => $schedule->edu_year_id
            ])
            ->count();

        if ($countThisSemester >= self::MAX_SCHEDULES_PER_SEMESTER) {
            $errors[] = _e('You cannot enroll more than') . self::MAX_SCHEDULES_PER_SEMESTER . _e('schedules in a semester');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // set derived fields per migration comments
        $model->circle_id = $schedule->circle_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (empty($errors)) {
            if ($model->save()) {
                // keep schedule student_count in sync
                $schedule->student_count = self::find()->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])->count();
                $schedule->save(false);

                $transaction->commit();
                return true;
            } else {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $transaction->rollBack();
        return simplify_errors($errors);
    }

    /**
     * Talabaning Circle (to‘garak) schedule yozuvini yangilash
     *
     * @param CircleEnrollment $model  // mavjud enrollment modeli
     * @param array $post              // request'dan keladigan ma'lumotlar
     * @return bool|array              // true yoki xatolar massivi
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        try {
            // 🆔 Student ID ni yangilash (faqat admin yoki o‘qituvchi)
            if (!isRole('student')) {
                if (empty($post['student_id'])) {
                    return simplify_errors([_e('Student id is required.')]);
                }
                $model->student_id = $post['student_id'];
            }

            // 🔄 Agar circle_schedule_id yangilanayotgan bo‘lsa
            if (!empty($post['circle_schedule_id'])) {
                $model->circle_schedule_id = $post['circle_schedule_id'];
            }

            // 🔍 Validatsiya
            if (!$model->validate()) {
                return simplify_errors($model->errors);
            }

            // 👤 student_user_id yangilash
            $model->student_user_id = $model->student->user_id;
            $model->circle_id = $model->circleSchedule->circle_id;
            $schedule = $model->circleSchedule;

            // ✅ 1) Max student limit check
            $currentCount = self::find()
                ->where(['circle_schedule_id' => $model->circle_schedule_id, 'is_deleted' => 0])
                ->andWhere(['!=', 'id', $model->id]) // o‘zini hisobga olmasin
                ->count();

            if ($currentCount >= (int) $schedule->max_student_count) {
                $errors[] = _e('Schedule capacity reached');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // ✅ 2) Bir xil Circle ni qayta tanlashni bloklash
            $existsSameCircle = self::find()
                ->alias('cs')
                ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                ->where([
                    'cs.student_id'   => $model->student_id,
                    'cs.is_deleted'   => 0,
                    'sch.circle_id'   => $schedule->circle_id,
                    'sch.edu_year_id' => $schedule->edu_year_id
                ])
                ->andWhere(['!=', 'cs.id', $model->id]) // o‘zidan tashqari
                ->exists();

            if ($existsSameCircle) {
                $errors[] = _e('You already enrolled to this circle');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // ✅ 3) Semestrdagi max schedule limiti
            $countThisSemester = self::find()
                ->alias('cs')
                ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                ->where([
                    'cs.student_id'   => $model->student_id,
                    'cs.is_deleted'   => 0,
                    'sch.edu_year_id' => $schedule->edu_year_id
                ])
                ->andWhere(['!=', 'cs.id', $model->id]) // o‘zidan tashqari
                ->count();

            if ($countThisSemester >= self::MAX_SCHEDULES_PER_SEMESTER) {
                $errors[] = _e('You cannot enroll more than') . ' ' .
                    self::MAX_SCHEDULES_PER_SEMESTER . ' ' .
                    _e('schedules in a semester');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // 🔄 Circle_id ni yangilash
            $model->circle_id = $schedule->circle_id;

            // Yana validatsiya
            if (!$model->validate()) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // 📝 Saqlash
            if ($model->save()) {
                // student_count ni yangilash
                $schedule->student_count = self::find()
                    ->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])
                    ->count();

                $schedule->save(false);

                $transaction->commit();
                return true;
            } else {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $errors[] = $e->getMessage();
            return simplify_errors($errors);
        }
    }


    public static function updateItemOld($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (empty($errors)) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        return simplify_errors($errors);
    }

    public static function rejectCertificate($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $model->certificate_status = 0;
        if ($model->save(false)) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($model->errors);
        }
        return true;
    }


    public static function generateCertificateTest($model)
    {
        $errors = [];
        $transaction = Yii::$app->db->beginTransaction();

        if (isRole('student')) {
            if ($model->student_user_id != current_user_id()) {
                $errors[] = _e('You are not authorized to generate certificate');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            if ($model->circleSchedule->abs_count > $model->absCount) {
                $errors[] = _e('Absence too much to get certificate');
                $transaction->rollBack();
                return simplify_errors($errors);
            } else {
                $model->abs_status = 1;
                $model->save(false);
            }

            if ($model->is_finished == 0) {
                $errors[] = _e('You have not finished yet');
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            if ($model->certificate_status == 1) {
                $errors[] = _e('You have already got certificate');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        try {
            $circleName   = $model->circle->translate->name ?? "Psixologiya asoslari";
            $studentName  = $model->student->fullName ?? "ZOIROVA SUG‘DIYONA SHUXRAT QIZI";
            $eduYear      = $model->circleSchedule->eduYear->name ?? "2024-2025";
            $semesterName = $model->circleSchedule->eduYear->type == 1 ? "Kuzgi" : "Bahorgi";
            $certDate     = date('Y-m-d');

            $text = "Qarshi xalqaro universitetida " . $eduYear . " o‘quv yili \"" . $semesterName . "\" semestrida tashkil etilgan <b style=\"color: #1F3468;\"> “" . $circleName . "”</b> to‘garagida muvaffaqiyatli ishtirok etgani uchun taqdim etildi.";

            // Fayl papkasi
            $path = '/uploads/certificates/';
            $dir = STORAGE_PATH . $path;
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new \Exception(_e('Failed to create certificate directory.'));
                }
            }

            $fileName = 'certificate_' . $model->id . '_' . current_user_id() . '_' . time() . '.pdf';
            $filePath = $dir . $fileName;
            $fileUrl  = 'storage' . $path . $fileName;

            // ⚡️ mPDF
            try {
                $mpdf = new \Mpdf\Mpdf([
                    'format' => 'A4-L',
                    'margin_left' => 0,
                    'margin_right' => 0,
                    'margin_top' => 0,
                    'margin_bottom' => 0,
                ]);
            } catch (\Mpdf\MpdfException $e) {
                return simplify_errors([_e('Failed to initialize PDF generator: ') . $e->getMessage()]);
            }

            // Shablon PNG rasmi
            $bgImage = Yii::getAlias('@webroot/templates/template.png');
            if (!file_exists($bgImage)) {
                return simplify_errors([_e('Certificate template image not found.')]);
            }

            ob_start();
?>
            <div style="position: relative; width: 100%; height: 100%; font-family: sans-serif;
                            background: url('<?= $bgImage ?>') no-repeat center center; 
                            background-size: cover;">
                <table style="width: 100%; height: 100%;">
                    <tr>
                        <td colspan="2" style="width: 88%; text-align: center; padding-left: 10px;">
                        </td>
                        <td style="width: 12%; text-align: end; padding-right: 10px;">
                            <barcode code="https://digital.kiu.uz/certificate/<?= $model->id ?>" type="QR" size="1.2" error="M" class="barcode" />
                            <br>
                            &nbsp;&nbsp; &nbsp;<?= $certDate ?>
                        </td>
                    </tr>
                    <div>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                    </div>

                    <tr>
                        <td colspan="3" style="text-align: center; padding-top: 10px;">
                            <h2 style="font-style: italic;  font-size: 30px; font-family: serif; color: #1F3468;"><?= strtoupper($studentName) ?></h2>

                        </td>
                    </tr>
                    <div>
                        <br>
                    </div>
                    <tr style="margin-top: 100px;">
                        <td style="width: 10%;"></td>
                        <td style="text-align: center; padding-top: 10px; ">
                            <h1 style="font-size: 26px; width: 80%; margin-right: 100px; font-family: Bahnschrift SemiLight Condensed; color: #666666;"><?= $text ?></h1>

                        </td>
                        <td></td>
                    </tr>

                </table>
                <div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                </div>
                <table>

                    <tr>
                        <td style="width: 11%;"></td>
                        <td style="width: 13%;">F.Haqqulov</td>
                        <td style="width: 6%;"></td>
                        <td style="width: 14%;"> Tr.Shermatov Javoxir</td>
                        <td style="width: 14%;"></td>
                        <td style="width: 14%;">Sh.Turdiyev</td>
                        <td style="width: 14%;"></td>
                    </tr>
                </table>
            </div>
            <?php
            $html = ob_get_clean();

            try {
                $mpdf->WriteHTML($html);
                $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
            } catch (\Exception $e) {
                $errors[] = _e('Failed to generate or save PDF: ') . $e->getMessage();
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // Model update
            $model->certificate_status = 1;
            $model->certificate_file = $fileUrl;
            $model->certificate_date = $certDate;
            if (!$model->save(false)) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            $errors[] = _e('Certificate generation error: ') . $e->getMessage();
            return simplify_errors($errors);
        }
    }


    public static function generateCertificate($model)
    {
        try {
            $circleName   = $model->circle->translate->name ?? "Psixologiya asoslari";
            $studentName  = $model->student->fullName ?? "ZOIROVA SUG‘DIYONA SHUXRAT QIZI";
            $eduYear      = $model->circleSchedule->eduYear->name ?? "2024-2025";
            $semesterName = $model->circleSchedule->eduYear->type == 1 ? "Kuzgi" : "Bahorgi";
            $certDate     = date('Y-m-d');

            $text = "Qarshi xalqaro universitetida " . $eduYear . " o‘quv yili \"" . $semesterName . "\" semestrida tashkil etilgan <b style=\"color: #1F3468;\"> “" . $circleName . "”</b> to‘garagida muvaffaqiyatli ishtirok etgani uchun taqdim etildi.";

            // Fayl papkasi
            $path = '/uploads/certificates/';
            $dir = STORAGE_PATH . $path;
            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new \Exception(_e('Failed to create certificate directory.'));
                }
            }

            $fileName = 'certificate_' . $model->id . '_' . current_user_id() . '_' . time() . '.pdf';
            $filePath = $dir . $fileName;
            $fileUrl  = 'storage' . $path . $fileName;

            // ⚡️ mPDF
            try {
                $mpdf = new \Mpdf\Mpdf([
                    'format' => 'A4-L',
                    'margin_left' => 0,
                    'margin_right' => 0,
                    'margin_top' => 0,
                    'margin_bottom' => 0,
                ]);
            } catch (\Mpdf\MpdfException $e) {
                return simplify_errors([_e('Failed to initialize PDF generator: ') . $e->getMessage()]);
            }

            // Shablon PNG rasmi
            $bgImage = Yii::getAlias('@webroot/templates/template.png');
            if (!file_exists($bgImage)) {
                return simplify_errors([_e('Certificate template image not found.')]);
            }

            ob_start();
            ?>
            <div style="position: relative; width: 100%; height: 100%; font-family: sans-serif;
                            background: url('<?= $bgImage ?>') no-repeat center center; 
                            background-size: cover;">
                <table style="width: 100%; height: 100%;">
                    <tr>
                        <td colspan="2" style="width: 88%; text-align: center; padding-left: 10px;">
                        </td>
                        <td style="width: 12%; text-align: end; padding-right: 10px;">
                            <barcode code="https://digital.kiu.uz/certificate/<?= $model->id ?>" type="QR" size="1.2" error="M" class="barcode" />
                            <br>
                            &nbsp;&nbsp; &nbsp;<?= $certDate ?>
                        </td>
                    </tr>
                    <div>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                    </div>

                    <tr>
                        <td colspan="3" style="text-align: center; padding-top: 10px;">
                            <h2 style="font-style: italic;  font-size: 30px; font-family: serif; color: #1F3468;"><?= strtoupper($studentName) ?></h2>

                        </td>
                    </tr>
                    <div>
                        <br>
                    </div>
                    <tr style="margin-top: 100px;">
                        <td style="width: 10%;"></td>
                        <td style="text-align: center; padding-top: 10px; ">
                            <h1 style="font-size: 26px; width: 80%; margin-right: 100px; font-family: Bahnschrift SemiLight Condensed; color: #666666;"><?= $text ?></h1>

                        </td>
                        <td></td>
                    </tr>

                </table>
                <div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                </div>
                <table>

                    <tr>
                        <td style="width: 11%;"></td>
                        <td style="width: 13%;">F.Haqqulov</td>
                        <td style="width: 6%;"></td>
                        <td style="width: 14%;"> Tr.Shermatov Javoxir</td>
                        <td style="width: 14%;"></td>
                        <td style="width: 14%;">Sh.Turdiyev</td>
                        <td style="width: 14%;"></td>
                    </tr>
                </table>
            </div>
        <?php
            $html = ob_get_clean();

            try {
                $mpdf->WriteHTML($html);
                $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);
            } catch (\Exception $e) {
                return simplify_errors([_e('Failed to generate or save PDF: ') . $e->getMessage()]);
            }

            // Model update
            $model->certificate_status = 1;
            $model->certificate_file = $fileUrl;
            $model->certificate_date = $certDate;
            if (!$model->save(false)) {
                return simplify_errors([_e('Failed to update certificate info in database.'), $model->errors]);
            }
        } catch (\Exception $e) {
            return simplify_errors([_e('Certificate generation error: ') . $e->getMessage()]);
        }
    }

    public static function generateCertificateOrginal($model)
    {
        $circleName   = $model->circle->translate->name ?? "Psixologiya asoslari";
        $studentName  = $model->student->fullName ?? "ZOIROVA SUG‘DIYONA SHUXRAT QIZI";
        $eduYear      = $model->circleSchedule->eduYear->name ?? "2024-2025";
        $semesterName = $model->circleSchedule->eduYear->type == 1 ? "Kuzgi" : "Bahorgi";
        $certDate     = date('Y-m-d');


        $text = "Qarshi xalqaro universitetida " . $eduYear . " o‘quv yili \"" . $semesterName . "\" semestrida tashkil etilgan <b style=\"color: #1F3468;\"> “" . $circleName . "”</b> to‘garagida muvaffaqiyatli ishtirok etgani uchun taqdim etildi.";


        // Fayl papkasi
        $path = '/uploads/certificates/';
        $dir = STORAGE_PATH . $path;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = 'certificate_' . $model->id . '_' . current_user_id() . '_' . time() . '.pdf';
        $filePath = $dir . $fileName;
        $fileUrl  = 'storage' . $path . $fileName;

        // ⚡️ mPDF
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        // Shablon PNG rasmi
        $bgImage = Yii::getAlias('@webroot/templates/template.png');

        ob_start();
        ?>
        <div style="position: relative; width: 100%; height: 100%; font-family: sans-serif;
                    background: url('<?= $bgImage ?>') no-repeat center center; 
                    background-size: cover;">
            <table style="width: 100%; height: 100%;">
                <tr>
                    <td colspan="2" style="width: 88%; text-align: center; padding-left: 10px;">
                    </td>
                    <td style="width: 12%; text-align: end; padding-right: 10px;">
                        <barcode code="https://digital.kiu.uz/certificate/<?= $model->id ?>" type="QR" size="1.2" error="M" class="barcode" />
                        <br>
                        &nbsp;&nbsp; &nbsp;<?= $certDate ?>
                    </td>
                </tr>
                <div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                </div>

                <tr>
                    <td colspan="3" style="text-align: center; padding-top: 10px;">
                        <h2 style="font-style: italic;  font-size: 30px; font-family: serif; color: #1F3468;"><?= strtoupper($studentName) ?></h2>

                    </td>
                </tr>
                <div>
                    <br>
                </div>
                <tr style="margin-top: 100px;">
                    <td style="width: 10%;"></td>
                    <td style="text-align: center; padding-top: 10px; ">
                        <h1 style="font-size: 26px; width: 80%; margin-right: 100px; font-family: Bahnschrift SemiLight Condensed; color: #666666;"><?= $text ?></h1>

                    </td>
                    <td></td>
                </tr>

            </table>
            <div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            </div>
            <table>

                <tr>
                    <td style="width: 11%;"></td>
                    <td style="width: 13%;">F.Haqqulov</td>
                    <td style="width: 6%;"></td>
                    <td style="width: 14%;"> Tr.Shermatov Javoxir</td>
                    <td style="width: 14%;"></td>
                    <td style="width: 14%;">Sh.Turdiyev</td>
                    <td style="width: 14%;"></td>
                </tr>
            </table>
        </div>
<?php
        $html = ob_get_clean();

        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // Model update
        $model->certificate_status = 1;
        $model->certificate_file = $fileUrl;
        $model->certificate_date = $certDate;
        $model->save(false);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Course bo'yicha studentlarni avtomatik circle_student ga qo'shish
     * 
     * @param int $courseId
     * @return array|bool
     */
    public static function autoEnrollStudentsByCourse($courseId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $enrolledCount = 0;
        $skippedCount = 0;

        try {
            // 1. Course bo'yicha faol studentlarni olish
            $students = Student::find()
                ->alias('s')
                ->innerJoin('users u', 'u.id = s.user_id')
                ->where([
                    's.course_id' => $courseId,
                    's.is_deleted' => 0,
                    'u.status' => 10, // faol foydalanuvchilar
                ])
                ->all();

            if (empty($students)) {
                $transaction->rollBack();
                return simplify_errors([_e('No active students found for this course.')]);
            }

            // 2. Hozirgi o'quv yili va semestrni aniqlash
            $currentYear = date('Y');
            $currentMonth = (int)date('n');
            $semesterType = ($currentMonth >= 9 || $currentMonth <= 1) ? 1 : 2; // 1=kuz, 2=bahor
            $semesterType = 1;

            // Faol o'quv yilini topish
            $activeEduYear = EduYear::find()
                ->where(['is_deleted' => 0, 'status' => 1])
                ->orderBy(['id' => SORT_DESC])
                ->one();

            if (!$activeEduYear) {
                $transaction->rollBack();
                return simplify_errors([_e('No active education year found.')]);
            }

            // 3. Circle schedule larni olish (30 tadan kam studentlari bor)
            $availableSchedules = CircleSchedule::find()
                ->alias('cs')
                ->leftJoin('circle c', 'c.id = cs.circle_id')
                ->where([
                    'cs.edu_year_id' => $activeEduYear->id,
                    'cs.semestr_type' => $semesterType,
                    'cs.is_deleted' => 0,
                    'c.is_deleted' => 0,
                ])
                ->andWhere(['<', 'cs.student_count', CircleSchedule::$max_student_count])
                ->orderBy(['cs.student_count' => SORT_ASC]) // eng kam studentlari borlarini birinchi
                ->all();

            // dd([
            //     'availableSchedules' => $availableSchedules,
            //     'students' => $students,
            //     'activeEduYear' => $activeEduYear->id,
            //     'semesterType' => $semesterType,
            // ]);

            if (empty($availableSchedules)) {
                $transaction->rollBack();
                return simplify_errors([_e('No available circle schedules found.')]);
            }

            // 4. Har bir student uchun circle_student qo'shish
            foreach ($students as $student) {
                // Studentning hozirgi semestrdagi circle_student sonini hisoblash
                $currentEnrollments = self::find()
                    ->alias('cs')
                    ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                    ->where([
                        'cs.student_id' => $student->id,
                        'cs.is_deleted' => 0,
                        'sch.edu_year_id' => $activeEduYear->id,
                        'sch.semestr_type' => $semesterType,
                    ])
                    ->count();

                // Qancha qo'shish kerakligini hisoblash
                $neededEnrollments = 2 - $currentEnrollments;

                if ($neededEnrollments <= 0) {
                    $skippedCount++;
                    continue; // Student allaqachon 2 ta circle ga yozilgan
                }

                // Student uchun mavjud schedule larni topish
                $enrolledCircles = [];
                $attempts = 0;
                $maxAttempts = count($availableSchedules) * 2; // cheksiz loop ni oldini olish

                while ($neededEnrollments > 0 && $attempts < $maxAttempts) {
                    foreach ($availableSchedules as $schedule) {
                        // Bu student allaqachon bu circle ga yozilmaganligini tekshirish
                        $alreadyEnrolled = self::find()
                            ->where([
                                'student_id' => $student->id,
                                'circle_schedule_id' => $schedule->id,
                                'is_deleted' => 0,
                            ])
                            ->exists();

                        if ($alreadyEnrolled) {
                            continue; // Bu circle ga allaqachon yozilgan
                        }

                        // Schedule da joy borligini tekshirish
                        if ($schedule->student_count >= CircleSchedule::$max_student_count) {
                            continue; // Schedule to'lgan
                        }

                        // Yangi circle_student yaratish
                        $newEnrollment = new self();
                        $newEnrollment->student_id = $student->id;
                        $newEnrollment->student_user_id = $student->user_id;
                        $newEnrollment->circle_schedule_id = $schedule->id;
                        $newEnrollment->circle_id = $schedule->circle_id;
                        $newEnrollment->status = 1;
                        $newEnrollment->is_finished = 0;
                        $newEnrollment->abs_status = 0;
                        $newEnrollment->certificate_status = 0;

                        if ($newEnrollment->save()) {
                            // Schedule student_count ni yangilash
                            $schedule->student_count = self::find()
                                ->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])
                                ->count();
                            $schedule->save(false);

                            $enrolledCount++;
                            $neededEnrollments--;
                            $enrolledCircles[] = $schedule->circle->translate->name ?? 'Unknown Circle';

                            if ($neededEnrollments <= 0) {
                                break; // Student uchun yetarli
                            }
                        } else {
                            $errors[] = "Failed to enroll student {$student->id} to schedule {$schedule->id}: " . json_encode($newEnrollment->errors);
                        }
                    }
                    $attempts++;
                }

                if ($neededEnrollments > 0) {
                    $errors[] = "Student {$student->id} could not be enrolled to enough circles. Needed: {$neededEnrollments}";
                }
            }

            if (empty($errors)) {
                $transaction->commit();
                return [
                    'enrolled_count' => $enrolledCount,
                    'skipped_count' => $skippedCount,
                    'message' => "Successfully enrolled {$enrolledCount} students to circles. Skipped {$skippedCount} students who already had 2 enrollments."
                ];
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return simplify_errors([_e('Auto enrollment error: ') . $e->getMessage()]);
        }
    }
}
