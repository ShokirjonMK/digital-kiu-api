<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use yii\behaviors\TimestampBehavior;
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
        return [
            'id',
            'circle_id',
            'circle_schedule_id',
            'student_user_id',
            'student_id',
            'is_finished',
            'abs_status',
            'certificate_status',
            'certificate_file',
            'certificate_date',
            'status',
            'created_at',
            'updated_at',
            'is_deleted',
            'created_by',
            'updated_by',
        ];
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




    public static function generateCertificate($model)
    {
        $circle = $model->circle; // expects relation
        $student = $model->student; // expects relation

        $circleName = ($circle && $circle->translate) ? ($circle->translate->name ?? '') : '';
        $studentName = '';
        if ($student) {
            $studentName = $student->fullName
                ?? trim(($student->last_name ?? '') . ' ' . ($student->first_name ?? '') . ' ' . ($student->middle_name ?? ''));
        }

        $qrUrl = 'https://domen.uz/circle-student/' . $model->id;
        $qrImg = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrUrl);

        $logoUrl = Yii::$app->params['certificateLogo'] ?? null; // optional logo path/url

        $html = '<html><head><meta charset="UTF-8"><style>
            body { font-family: DejaVu Sans, sans-serif; }
            .page { padding: 40px; position: relative; min-height: 100%; }
            .top-left { position: absolute; left: 40px; top: 40px; }
            .title { text-align: center; margin-top: 120px; font-size: 28px; font-weight: bold; }
            .student { text-align: center; margin-top: 20px; font-size: 20px; }
            .bottom-right { position: absolute; right: 40px; bottom: 40px; text-align: right; }
        </style></head><body><div class="page">';

        if (!empty($logoUrl)) {
            $html .= '<div class="top-left"><img src="' . htmlspecialchars($logoUrl) . '" height="60"/></div>';
        }

        $html .= '<div class="title">' . htmlspecialchars($circleName) . '</div>';
        $html .= '<div class="student">' . htmlspecialchars($studentName) . '</div>';
        $html .= '<div class="bottom-right"><img src="' . htmlspecialchars($qrImg) . '" height="120"/><div style="font-size:10px">' . htmlspecialchars($qrUrl) . '</div></div>';
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

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
