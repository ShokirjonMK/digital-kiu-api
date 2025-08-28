<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use yii\behaviors\TimestampBehavior;

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
            [['circle_id', 'circle_schedule_id', 'student_user_id', 'student_id', 'is_finished', 'abs_status', 'certificate_status', 'certificate_date', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
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
        return $this->hasMany(CircleAttendance::className(), ['circle_student_id' => 'id']);
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

    public static function generateCertificate($model)
    {
        $circleName  = "To'garak nomi"; // yoki $model->circle->name
        $studentName = "Ism Familiya";  // yoki $model->student->full_name
        $certDate    = date('Y-m-d');

        // Fayl papkasi va yo‘llar
        $path = '/uploads/certificates/';
        $dir = STORAGE_PATH . $path;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true); // agar yo‘q bo‘lsa papkani yaratamiz
        }

        $fileName = 'certificate_' . $model->id . '_' . current_user_id() . '_' . time() . '.pdf';
        $filePath = $dir . $fileName;
        $fileUrl  =  'storage' . $path . $fileName;


        // ⚡️ mPDF instance
        $mpdf = new Mpdf();

        ob_start();
?>
        <div style="position: relative; width:100%; height:100%; font-family: sans-serif;">

            <!-- Top-left logo -->
            <div style="position: absolute; top: 10px; left: 10px;">
                <img src="<?= Yii::getAlias('@web/images/logo.png') ?>" width="100">
            </div>

            <!-- Center text -->
            <div style="text-align: center; margin-top: 150px;">
                <h1 style="font-size: 32px; margin-bottom: 20px;"><?= $circleName ?></h1>
                <h2 style="font-size: 24px;"><?= $studentName ?></h2>
                <p style="margin-top: 30px;">Certificate Date: <?= $certDate ?></p>
            </div>

            <!-- Bottom-right QR -->
            <div style="position: absolute; bottom: 20px; right: 20px;">
                <barcode code="https://digital.kiu.uz/certificate/<?= $model->id ?>" type="QR" size="1.2" error="M" class="barcode" />
            </div>

        </div>
    <?php
        $html = ob_get_clean();

        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // Model update
        $model->certificate_file = $fileUrl;
        $model->certificate_date = $certDate;
        $model->save(false);
    }

    public static function generateCertificateTest($model)
    {
        $circleName   = $model->circle->name ?? "Psixologiya asoslari";
        $studentName  = $model->student->full_name ?? "ZOIROVA SUG‘DIYONA SHUXRAT QIZI";
        $eduYear      = $model->eduYear->name ?? "2024-2025-o‘quv yili";
        $semesterName = $model->eduYear->semester ?? "Bahorgi";
        $certDate     = date('Y-m-d');



        $text = "Qarshi xalqaro universitetida " . $eduYear . " \"" . $semesterName . "\" semestrida tashkil etilgan <b style=\"color: #1F3468;\"> “" . $circleName . "”</b> to‘garagida muvaffaqiyatli ishtirok etgani uchun taqdim etildi.";


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
        $model->certificate_file = $fileUrl;
        $model->certificate_date = $certDate;
        $model->save(false);
    }



    public static function generateCertificateTest1($model)
    {
        $circleName  = $model->circle->name ?? "To'garak nomi 333";
        $studentName = $model->student->full_name ?? "Ism Familiya 333";
        $certDate    = date('Y-m-d');

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
        $mpdf = new Mpdf([
            'format' => 'A4-L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

        // PDF shablon fon sifatida
        $template = Yii::getAlias('@webroot/templates/template.png');
        $mpdf->SetDocTemplate($template, true); // true => hamma sahifaga

        ob_start();
    ?>
        <div style="position: relative; width: 100%; height: 100%; font-family: sans-serif;">

            <!-- QR top-right -->
            <div style="position: absolute; top: 40px; right: 40px;">
                <barcode code="https://digital.kiu.uz/certificate/<?= $model->id ?>" type="QR" size="1.2" error="M" class="barcode" />
            </div>

            <!-- Circle name -->
            <div style="position: absolute; top: 320px; width: 100%; text-align: center;">
                <h1 style="font-size: 30px; font-weight: bold;"><?= $circleName ?></h1>
            </div>

            <!-- Student name -->
            <div style="position: absolute; top: 420px; width: 100%; text-align: center;">
                <h2 style="font-size: 26px;"><?= $studentName ?></h2>
            </div>

            <!-- Certificate date -->
            <div style="position: absolute; bottom: 80px; left: 100px;">
                <p style="font-size: 14px;"><?= $certDate ?></p>
            </div>
        </div>
    <?php
        $html = ob_get_clean();

        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        // Model update
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

    public static function generateCertificateTest123($model)
    {
        $circleName   = $model->circle->name ?? "To‘garak nomi";
        $studentName  = $model->student->full_name ?? "Ism Familiya";
        $eduYear      = $model->eduYear->name ?? "2024-2025-o‘quv yili";
        $semesterName = $model->eduYear->semester ?? "Bahorgi";
        $certDate     = date('Y-m-d');

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
            'format'        => 'A3',
            'orientation'   => 'L',
            'margin_left'   => 0,
            'margin_right'  => 0,
            'margin_top'    => 0,
            'margin_bottom' => 0,
        ]);

        // Shablon PDF
        $template = Yii::getAlias('@webroot/templates/certificate_template.pdf');
        $mpdf->SetDocTemplate($template, true);

        ob_start();
    ?>
        <div style="position: relative; width: 100%; height: 100%; font-family: sans-serif;">

            <!-- QR code top-left -->
            <div style="position: absolute; top: 20px; left: 20px;">
                <barcode code="https://digital.kiu.uz/certificate/<?= $model->id ?>" type="QR" size="1.0" error="M" class="barcode" />
            </div>

            <!-- Student name -->
            <div style="position: absolute; top: 58%; width: 100%; text-align: center;">
                <h2 style="font-size: 26px; font-weight: bold;"><?= strtoupper($studentName) ?></h2>
            </div>

            <!-- Circle name + EduYear -->
            <div style="position: absolute; top: 67%; width: 100%; text-align: center;">
                <p style="font-size: 18px; margin:0;">
                    Qarshi xalqaro universitetida <?= $eduYear ?> "<?= $semesterName ?>" semestrida tashkil etilgan
                    <b>“<?= $circleName ?>”</b> to‘garagida muvaffaqiyatli ishtirok etgani uchun taqdim etildi.
                </p>
            </div>

            <!-- Certificate date -->
            <div style="position: absolute; bottom: 60px; left: 100px;">
                <p style="font-size: 14px;"><?= $certDate ?></p>
            </div>
        </div>
<?php
        $html = ob_get_clean();

        $mpdf->WriteHTML($html);
        $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

        $model->certificate_file = $fileUrl;
        $model->certificate_date = $certDate;
        $model->save(false);
    }
}
