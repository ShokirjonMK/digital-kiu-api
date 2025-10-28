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
    const STATUS_MANUAL = 1;
    const STATUS_RANDOM = 2;

    public function rules()
    {
        return [
            [['circle_schedule_id'], 'required'],
            [['circle_id', 'edu_year_id', 'semestr_type', 'circle_schedule_id', 'student_user_id', 'student_id', 'is_finished', 'abs_status', 'certificate_status', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['certificate_file'], 'string', 'max' => 255],
            [['certificate_date'], 'safe'],
            [['circle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Circle::className(), 'targetAttribute' => ['circle_id' => 'id']],
            [['circle_schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => CircleSchedule::className(), 'targetAttribute' => ['circle_schedule_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],

            // Custom validation for unique constraint considering is_deleted = 0
            ['student_id', 'validateUniqueEnrollment'],
            ['circle_schedule_id', 'validateUniqueScheduleEnrollment'],

            // agar circle finished_status 1 bo'lsa, is_finished o'zgartirish mumkin aks holda xatolik chiqishi kerak
            ['is_finished', 'validateIsFinished'],

        ];
    }


    public function validateIsFinished($attribute, $params)
    {
        if ($this->isAttributeChanged($attribute)) {
            if ($this->circle && $this->circle->finished_status != 1) {
                $this->addError($attribute, 'Circle hali tugallanmaganligi sababli bu maydonni o‘zgartirish mumkin emas.');
            }
        }
    }

    /**
     * Validate unique enrollment for student in circle (only when is_deleted = 0)
     */
    public function validateUniqueEnrollment($attribute, $params)
    {
        if ($this->isNewRecord || $this->isAttributeChanged($attribute) || $this->isAttributeChanged('circle_id')) {
            $query = self::find()
                ->where([
                    'student_id' => $this->student_id,
                    'circle_id' => $this->circle_id,
                    'is_deleted' => 0
                ]);

            if (!$this->isNewRecord) {
                $query->andWhere(['!=', 'id', $this->id]);
            }

            if ($query->exists()) {
                $this->addError($attribute, _e('Student already enrolled to this circle. ' . $this->circle_id . ' ' . $this->student_id));
            }
        }
    }

    /**
     * Validate unique enrollment for student in circle schedule (only when is_deleted = 0)
     */
    public function validateUniqueScheduleEnrollment($attribute, $params)
    {
        if ($this->isNewRecord || $this->isAttributeChanged($attribute) || $this->isAttributeChanged('student_user_id')) {
            $query = self::find()
                ->where([
                    'circle_schedule_id' => $this->circle_schedule_id,
                    'student_user_id' => $this->student_user_id,
                    'is_deleted' => 0
                ]);

            if (!$this->isNewRecord) {
                $query->andWhere(['!=', 'id', $this->id]);
            }

            if ($query->exists()) {
                $this->addError($attribute, _e('Student already enrolled to this schedule.'));
            }
        }
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

            'absCount',
            'absCountAll',
            'myAttendance',


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
        return $this->hasMany(CircleAttendance::class, ['circle_student_id' => 'id'])
            ->andWhere(['or', ['is_deleted' => 0], ['is_deleted' => null]]);
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

            if ($model->student->edu_form_id != 1) {
                $errors[] = _e('Student edu form is not allowed to enroll in circle.');
                $transaction->rollBack();
                return simplify_errors($errors);
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

            // ✅ 4) Talabaning vaqt to'qnashuvi tekshiruvi (time conflict check)
            $existingSchedules = self::find()
                ->alias('cs')
                ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                ->where([
                    'cs.student_id'   => $model->student_id,
                    'cs.is_deleted'   => 0,
                    'sch.edu_year_id' => $schedule->edu_year_id,
                    'sch.week_id'     => $schedule->week_id, // same day of week
                ])
                ->select(['sch.start_time', 'sch.end_time', 'sch.id'])
                ->asArray()
                ->all();

            foreach ($existingSchedules as $existing) {
                $existingStart = $existing['start_time'];
                $existingEnd   = $existing['end_time'];
                $newStart      = $schedule->start_time;
                $newEnd        = $schedule->end_time;

                // Vaqt to'qnashuvi: start1 < end2 AND start2 < end1
                if ($newStart < $existingEnd && $existingStart < $newEnd) {
                    $errors[] = _e('You have a schedule conflict on this day and time') .
                        ' (' . $existingStart . ' - ' . $existingEnd . ')';
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }

            // 🔄 Circle_id ni schedule'dan avtomatik olish
            $model->circle_id = $schedule->circle_id;
            $model->edu_year_id = $schedule->edu_year_id;
            $model->semestr_type = $schedule->semestr_type;

            // Yana validatsiya (avvalgi xatoliklar bartaraf etilgan bo‘lsa)
            if (!$model->validate()) {
                $errors[] = $model->errors;
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            // 📝 Saqlash
            if ($model->save()) {
                // Schedule ichidagi student_count ni yangilash
                $schedule->updateStudentCount();

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

        if ($countThisSemester >= self::MAX_SCHEDULES_PER_SEMESTER && isRole('student')) {
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
                $schedule->updateStudentCount();

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

        if (isset($post['is_finished'])) {
            $model->is_finished = $post['is_finished'];

            if ($model->save(false)) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($model->errors);
            }
        }

        $errors[] = _e('There is an error occurred while processing.');
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function isFinished($model, $isFinished)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!isRole('admin')) {
            if ($model->circleSchedule->teacher_user_id !== current_user_id()) {
                $errors[] = _e('You are not authorized to give finished status.');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        $model->is_finished = $isFinished;

        if ($model->save(false)) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($model->errors);
        }

        $errors[] = _e('There is an error occurred while processing.');
        $transaction->rollBack();
        return simplify_errors($errors);
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


    /**
     * Course bo'yicha studentlarni avtomatik circle_student ga qo'shish
     * 
     * @param int $courseId
     * @return array|bool
     */
    public static function autoEnrollStudentsByCourseOld($courseId)
    {
        $data = [];
        $data['status'] = 1;
        $data['message'] = 'Students enrolled successfully.';
        $data['added'] = [];

        $eduYearId = EduYear::find()
            ->where(['is_deleted' => 0, 'status' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one()
            ->id;
        $added = [];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1️⃣ Shu yil bo‘yicha circle_student 2 tadan kam bo‘lgan studentlar
            // Find students in the course who have less than 2 circle_student records for this year
            $students = Student::find()
                ->alias('s')
                ->where(['s.status' => 10, 's.course_id' => $courseId])
                ->andWhere([
                    '<',
                    '(SELECT COUNT(*) FROM circle_student cs WHERE cs.student_id = s.id AND cs.edu_year_id = :eduYearId AND cs.is_deleted = 0)',
                    2
                ])
                ->addParams([':eduYearId' => $eduYearId])
                ->all();


            foreach ($students as $student) {

                // 2️⃣ Studentning joriy yildagi circle_student sonini tekshirish
                $currentYearCount = CircleStudent::find()
                    ->where([
                        'student_id' => $student->id,
                        'edu_year_id' => $eduYearId,
                        'is_deleted' => 0
                    ])
                    ->count();

                // Hali nechta kerak?
                $need = 2 - $currentYearCount;

                if ($need <= 0) {
                    continue;
                }

                // 2.1️⃣ Get ALL active circle enrollments (across all years) to avoid duplicate circle_id
                // This is needed because validateUniqueEnrollment checks across all years
                $takenCircleIds = CircleStudent::find()
                    ->alias('cs')
                    ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                    ->where([
                        'cs.student_id' => $student->id,
                        'cs.is_deleted' => 0
                    ])
                    ->select('sch.circle_id')
                    ->distinct()
                    ->column();

                // 3️⃣ Mos bo'sh circle_schedule larini olish
                // Har bir circle_id bo'yicha faqat bittadan schedule olish
                $query = CircleSchedule::find()
                    ->where([
                        'edu_year_id' => $eduYearId,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                    ->andWhere(['<', 'student_count', new \yii\db\Expression('max_student_count')]);

                // Exclude circles the student is already enrolled in
                if (!empty($takenCircleIds)) {
                    $query->andWhere(['not in', 'circle_id', $takenCircleIds]);
                }

                $schedules = $query->orderBy([
                    'student_count' => SORT_ASC,
                    // new \yii\db\Expression('RAND()')
                ])
                    ->asArray()
                    ->all();

                // Har bir circle_id bo'yicha faqat bittadan schedule tanlab olish
                $uniqueSchedules = [];
                foreach ($schedules as $schedule) {
                    if (!isset($uniqueSchedules[$schedule['circle_id']])) {
                        $uniqueSchedules[$schedule['circle_id']] = $schedule;
                    }
                    if (count($uniqueSchedules) >= $need) {
                        break;
                    }
                }
                // Obyektga aylantirish
                $schedules = [];
                foreach ($uniqueSchedules as $row) {
                    $schedules[] = CircleSchedule::findOne($row['id']);
                }



                // 4️⃣ Studentni yozib chiqish
                foreach ($schedules as $schedule) {
                    $circleStudent = new CircleStudent();
                    $circleStudent->student_id        = $student->id;
                    $circleStudent->student_user_id   = $student->user_id;
                    $circleStudent->circle_id         = $schedule->circle_id;
                    $circleStudent->circle_schedule_id = $schedule->id;
                    $circleStudent->edu_year_id       = $eduYearId;
                    $circleStudent->created_by        = Yii::$app->user->id ?? 0;
                    $circleStudent->updated_by        = Yii::$app->user->id ?? 0;
                    $circleStudent->created_at        = time();
                    $circleStudent->updated_at        = time();
                    $circleStudent->status            = self::STATUS_RANDOM;
                    $circleStudent->semestr_type      = $schedule->semestr_type;

                    if (!$circleStudent->save()) {
                        throw new \Exception(json_encode($circleStudent->errors));
                    }

                    // student_count ni yangilash
                    $schedule->updateStudentCount();

                    $added[] = [
                        'student_id' => $student->id,
                        'circle_id' => $schedule->circle_id,
                        'schedule_id' => $schedule->id
                    ];
                }
            }
            $data['added'] = $added;
            $transaction->commit();
            return $data;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $data['status'] = 0;
            $data['message'] = 'Error';
            $data['error'] = $e->getMessage();
            return $data;
        }
    }

    public static function autoEnrollStudentsByCourseWorks($courseId)
    {
        $res = ['status' => 1, 'message' => 'Students enrolled successfully.', 'added' => []];

        // 0) Joriy o‘quv yili
        $eduYear = EduYear::find()
            ->where(['is_deleted' => 0, 'status' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (!$eduYear) {
            return ['status' => 0, 'message' => 'EduYear not found'];
        }

        $eduYearId = (int)$eduYear->id;
        $db = Yii::$app->db;
        $tx = $db->beginTransaction();

        try {
            // 1) Shu kursdagi, joriy yilda 2 tadan kam CircleStudent yozuvi bor talabalar
            $students = Student::find()->alias('s')
                ->where(['s.status' => 10, 's.course_id' => (int)$courseId])
                ->andWhere([
                    '<',
                    new \yii\db\Expression(
                        '(SELECT COUNT(*) FROM circle_student cs
                      WHERE cs.student_id = s.id
                        AND cs.edu_year_id = :ey
                        AND cs.is_deleted = 0)'
                    ),
                    2
                ])
                ->andWhere(['s.edu_form_id' => 1])
                ->addParams([':ey' => $eduYearId])
                ->all();

            foreach ($students as $st) {
                // 2) Joriy yil uchun mavjud yozuvlar soni
                $current = (int)CircleStudent::find()
                    ->where([
                        'student_id'  => (int)$st->id,
                        'edu_year_id' => $eduYearId,
                        'is_deleted'  => 0,
                    ])->count();

                $need = 2 - $current;
                if ($need <= 0) {
                    continue;
                }

                // 3) Kandidat schedule'lar:
                //   - Joriy yil, aktiv, o'chirilmagan
                //   - Capacity bor (student_count < max_student_count)
                //   - AYNAN shu student uchun CircleStudent'da BOR circle_id-lar CHIQARILADI (NOT EXISTS)
                //   - Talabaning mavjud to'garaklari bilan vaqt to'qnashuvi YO'Q (week_id, start_time, end_time)
                //   - Har circle_id uchun eng kichik student_count'li schedule olinadi (MIN)
                $sub = CircleSchedule::find()->alias('s')
                    ->select([
                        's.circle_id',
                        'min_sc' => new \yii\db\Expression('MIN(s.student_count)'),
                    ])
                    ->where([
                        's.edu_year_id' => $eduYearId,
                        's.status'      => 1,
                        's.is_deleted'  => 0,
                    ])
                    ->andWhere(new \yii\db\Expression('s.student_count < s.max_student_count'))
                    ->andWhere([
                        'NOT EXISTS',
                        CircleStudent::find()->alias('csx')
                            ->where('csx.circle_id = s.circle_id')
                            ->andWhere([
                                'csx.student_id' => (int)$st->id,
                                'csx.is_deleted' => 0,
                            ])
                    ])
                    // Vaqt to'qnashuvi yo'q: mavjud to'garaklarning vaqtlari bilan bir xil bo'lmagan
                    ->andWhere([
                        'NOT EXISTS',
                        CircleSchedule::find()->alias('existing_sch')
                            ->innerJoin(
                                'circle_student existing_cs',
                                'existing_cs.circle_schedule_id = existing_sch.id'
                            )
                            ->where([
                                'existing_cs.student_id' => (int)$st->id,
                                'existing_cs.edu_year_id' => $eduYearId,
                                'existing_cs.is_deleted' => 0,
                                'existing_sch.is_deleted' => 0,
                            ])
                            ->andWhere('existing_sch.week_id = s.week_id')
                            ->andWhere(new \yii\db\Expression(
                                '(s.start_time < existing_sch.end_time AND s.end_time > existing_sch.start_time)'
                            ))
                    ])
                    ->groupBy('s.circle_id');

                $candidates = CircleSchedule::find()->alias('sch')
                    ->innerJoin(['x' => $sub], 'x.circle_id = sch.circle_id AND x.min_sc = sch.student_count')
                    ->where([
                        'sch.edu_year_id' => $eduYearId,
                        'sch.status'      => 1,
                        'sch.is_deleted'  => 0,
                    ])
                    ->andWhere(new \yii\db\Expression('sch.student_count < sch.max_student_count'))
                    ->andWhere(['sch.building_id' => $st->direction->building_id])
                    ->andWhere([
                        'NOT EXISTS',
                        CircleStudent::find()->alias('csy')
                            ->where('csy.circle_id = sch.circle_id')
                            ->andWhere([
                                'csy.student_id' => (int)$st->id,
                                'csy.is_deleted' => 0,
                            ])
                    ])
                    // Vaqt to'qnashuvi yo'q: mavjud to'garaklarning vaqtlari bilan bir xil bo'lmagan
                    ->andWhere([
                        'NOT EXISTS',
                        CircleSchedule::find()->alias('existing_sch2')
                            ->innerJoin(
                                'circle_student existing_cs2',
                                'existing_cs2.circle_schedule_id = existing_sch2.id'
                            )
                            ->where([
                                'existing_cs2.student_id' => (int)$st->id,
                                'existing_cs2.edu_year_id' => $eduYearId,
                                'existing_cs2.is_deleted' => 0,
                                'existing_sch2.is_deleted' => 0,
                            ])
                            ->andWhere('existing_sch2.week_id = sch.week_id')
                            ->andWhere(new \yii\db\Expression(
                                '(sch.start_time < existing_sch2.end_time AND sch.end_time > existing_sch2.start_time)'
                            ))
                    ])
                    ->orderBy(['sch.student_count' => SORT_ASC, 'sch.id' => SORT_ASC])
                    ->limit($need)
                    ->all();

                if (!$candidates) {
                    continue;
                }

                // 4) Kiritish — atomar capacity lock + dublikatdan himoya
                foreach ($candidates as $schedule) {
                    // 4.1) Tezkor dublikat tekshiruvi: (student_id, circle_id, is_deleted=0)
                    $dup = CircleStudent::find()
                        ->where([
                            'student_id' => (int)$st->id,
                            'circle_id'  => (int)$schedule->circle_id,
                            'is_deleted' => 0,
                        ])->exists();
                    if ($dup) {
                        continue;
                    }

                    // 4.2) Atomar capacity band qilish
                    $affected = $db->createCommand(
                        'UPDATE circle_schedule
                       SET student_count = student_count + 1, updated_at = :ts
                     WHERE id = :id AND student_count < max_student_count',
                        [':ts' => time(), ':id' => (int)$schedule->id]
                    )->execute();

                    if ($affected === 0) {
                        // Shu orada joy tugagan bo‘lishi mumkin
                        continue;
                    }

                    // 4.3) CircleStudent yozuvi
                    $cs = new CircleStudent();
                    $cs->student_id         = (int)$st->id;
                    $cs->student_user_id    = (int)$st->user_id;
                    $cs->circle_id          = (int)$schedule->circle_id;
                    $cs->circle_schedule_id = (int)$schedule->id;
                    $cs->edu_year_id        = $eduYearId;
                    $cs->semestr_type       = $schedule->semestr_type;
                    $cs->status             = CircleStudent::STATUS_RANDOM;
                    $cs->created_by         = Yii::$app->user->id ?? 0;
                    $cs->updated_by         = Yii::$app->user->id ?? 0;
                    $cs->created_at         = time();
                    $cs->updated_at         = time();

                    if (!$cs->save()) {
                        // capacity’ni qaytarib qo‘yamiz
                        $db->createCommand(
                            'UPDATE circle_schedule
                           SET student_count = student_count - 1, updated_at = :ts
                         WHERE id = :id AND student_count > 0',
                            [':ts' => time(), ':id' => (int)$schedule->id]
                        )->execute();

                        // Agar dublikat validatsiyasi bo‘lsa — skip
                        $errText = implode(' | ', $cs->getFirstErrors());
                        if (stripos($errText, 'Student already enrolled to this circle') !== false) {
                            continue;
                        }

                        throw new \Exception(json_encode($cs->errors, JSON_UNESCAPED_UNICODE));
                    }

                    $res['added'][] = [
                        'student_id'  => (int)$st->id,
                        'circle_id'   => (int)$schedule->circle_id,
                        'schedule_id' => (int)$schedule->id,
                    ];
                }
            }

            $tx->commit();
            return $res;
        } catch (\Throwable $e) {
            $tx->rollBack();
            return ['status' => 0, 'message' => 'Error', 'error' => $e->getMessage()];
        }
    }
    /**
     * Talabalarni avtomatik to'garaklarga yozish (optimallashtirilgan)
     * 
     * @param int $courseId Kurs ID
     * @param int $batchSize Bir vaqtda qayta ishlanadigan talabalar soni
     * @return array
     */
    public static function autoEnrollStudentsByCourse($courseId, $batchSize = 100)
    {
        $res = ['status' => 1, 'message' => 'Students enrolled successfully.', 'added' => [], 'skipped' => 0];

        // 0) Joriy o'quv yilini olish
        $eduYear = EduYear::find()
            ->select(['id'])
            ->where(['is_deleted' => 0, 'status' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if (!$eduYear) {
            return ['status' => 0, 'message' => 'EduYear not found'];
        }

        $eduYearId = (int)$eduYear->id;
        $db = Yii::$app->db;

        try {
            // 1) 2 tadan kam to'garak bor talabalarni topish
            $students = self::getEligibleStudents($courseId, $eduYearId, $batchSize);

            if (empty($students)) {
                return ['status' => 1, 'message' => 'No eligible students found', 'added' => []];
            }

            $studentIds = array_column($students, 'id');

            // 2) Barcha talabalarning mavjud to'garaklarini bir martalik olish (optimizatsiya)
            $existingEnrollments = self::getExistingEnrollments($studentIds, $eduYearId);

            // 3) Har bir talaba uchun yozish jarayoni
            foreach ($students as $student) {
                $tx = $db->beginTransaction();

                try {
                    $studentId = (int)$student['id'];
                    $buildingId = (int)$student['building_id'];

                    // Mavjud yozuvlar soni
                    $currentCount = isset($existingEnrollments[$studentId])
                        ? count($existingEnrollments[$studentId])
                        : 0;

                    $need = 2 - $currentCount;

                    if ($need <= 0) {
                        $res['skipped']++;
                        $tx->commit();
                        continue;
                    }

                    // Talabaning band vaqtlarini olish
                    $busyTimes = isset($existingEnrollments[$studentId])
                        ? self::extractBusyTimes($existingEnrollments[$studentId])
                        : [];

                    // Mavjud circle_id'larni olish (dublikat oldini olish uchun)
                    $existingCircleIds = isset($existingEnrollments[$studentId])
                        ? array_column($existingEnrollments[$studentId], 'circle_id')
                        : [];

                    // 4) Kandidat schedule'larni topish
                    $candidates = self::getCandidateSchedules(
                        $eduYearId,
                        $studentId,
                        $buildingId,
                        $existingCircleIds,
                        $busyTimes,
                        $need
                    );

                    if (empty($candidates)) {
                        $res['skipped']++;
                        $tx->commit();
                        continue;
                    }

                    // 5) Talabani yozish
                    $enrolled = self::enrollStudent($studentId, $student['user_id'], $candidates, $eduYearId, $db);

                    $res['added'] = array_merge($res['added'], $enrolled);

                    $tx->commit();
                } catch (\Throwable $e) {
                    $tx->rollBack();

                    Yii::error([
                        'message' => 'Error enrolling student',
                        'student_id' => $studentId ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ], 'circle-enrollment-error');

                    continue;
                }
            }

            Yii::info([
                'course_id' => $courseId,
                'students_processed' => count($students),
                'enrolled' => count($res['added']),
                'skipped' => $res['skipped']
            ], 'circle-enrollment-success');

            return $res;
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'Critical error in autoEnrollStudentsByCourse',
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ], 'circle-enrollment-critical');

            return ['status' => 0, 'message' => 'Error', 'error' => $e->getMessage()];
        }
    }

    /**
     * 2 tadan kam to'garak bor talabalarni olish
     */
    private static function getEligibleStudents($courseId, $eduYearId, $limit)
    {
        return Student::find()
            ->alias('s')
            ->select(['s.id', 's.user_id', 'd.building_id'])
            ->innerJoin('direction d', 'd.id = s.direction_id')
            ->where([
                's.status' => 10,
                's.course_id' => (int)$courseId,
                's.edu_form_id' => 1,
                's.is_deleted' => 0
            ])
            ->andWhere([
                '<',
                new \yii\db\Expression(
                    '(SELECT COUNT(*) FROM circle_student cs
                      WHERE cs.student_id = s.id
                        AND cs.edu_year_id = :ey
                        AND cs.is_deleted = 0)'
                ),
                2
            ])
            ->addParams([':ey' => $eduYearId])
            ->limit($limit)
            ->asArray()
            ->all();
    }

    /**
     * Talabalarning mavjud to'garaklarini olish (optimizatsiya)
     */
    private static function getExistingEnrollments($studentIds, $eduYearId)
    {
        $enrollments = CircleStudent::find()
            ->alias('cs')
            ->select([
                'cs.student_id',
                'cs.circle_id',
                'cs.circle_schedule_id',
                'sch.week_id',
                'sch.start_time',
                'sch.end_time'
            ])
            ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
            ->where([
                'cs.student_id' => $studentIds,
                'cs.edu_year_id' => $eduYearId,
                'cs.is_deleted' => 0,
                'sch.is_deleted' => 0
            ])
            ->asArray()
            ->all();

        // Talaba bo'yicha guruhlash
        $grouped = [];
        foreach ($enrollments as $enr) {
            $grouped[$enr['student_id']][] = $enr;
        }

        return $grouped;
    }

    /**
     * Talabaning band vaqtlarini ajratib olish
     */
    private static function extractBusyTimes($enrollments)
    {
        $busyTimes = [];
        foreach ($enrollments as $enr) {
            $weekId = (int)$enr['week_id'];
            if (!isset($busyTimes[$weekId])) {
                $busyTimes[$weekId] = [];
            }
            $busyTimes[$weekId][] = [
                'start' => $enr['start_time'],
                'end' => $enr['end_time']
            ];
        }
        return $busyTimes;
    }

    /**
     * Vaqt to'qnashuvini tekshirish
     */
    private static function hasTimeConflict($weekId, $startTime, $endTime, $busyTimes)
    {
        if (!isset($busyTimes[$weekId])) {
            return false;
        }

        foreach ($busyTimes[$weekId] as $busy) {
            // Vaqt oraliqlarining kesishishini tekshirish
            if ($startTime < $busy['end'] && $endTime > $busy['start']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kandidat schedule'larni topish
     */
    private static function getCandidateSchedules($eduYearId, $studentId, $buildingId, $existingCircleIds, $busyTimes, $limit)
    {
        // Subquery: har bir circle uchun eng kam student_count'li schedule
        $subQuery = CircleSchedule::find()
            ->alias('s')
            ->select([
                's.circle_id',
                'min_count' => new \yii\db\Expression('MIN(s.student_count)')
            ])
            ->where([
                's.edu_year_id' => $eduYearId,
                's.status' => 1,
                's.is_deleted' => 0,
                's.building_id' => $buildingId
            ])
            ->andWhere(new \yii\db\Expression('s.student_count < s.max_student_count'))
            ->groupBy('s.circle_id');

        // Mavjud circle'larni chiqarib tashlash
        if (!empty($existingCircleIds)) {
            $subQuery->andWhere(['NOT IN', 's.circle_id', $existingCircleIds]);
        }

        // Asosiy query
        $query = CircleSchedule::find()
            ->alias('sch')
            ->select([
                'sch.id',
                'sch.circle_id',
                'sch.week_id',
                'sch.start_time',
                'sch.end_time',
                'sch.student_count',
                'sch.max_student_count',
                'sch.semestr_type'
            ])
            ->innerJoin(['sub' => $subQuery], 'sub.circle_id = sch.circle_id AND sub.min_count = sch.student_count')
            ->where([
                'sch.edu_year_id' => $eduYearId,
                'sch.status' => 1,
                'sch.is_deleted' => 0,
                'sch.building_id' => $buildingId
            ])
            ->andWhere(new \yii\db\Expression('sch.student_count < sch.max_student_count'))
            ->orderBy(['sch.student_count' => SORT_ASC, 'sch.id' => SORT_ASC]);

        if (!empty($existingCircleIds)) {
            $query->andWhere(['NOT IN', 'sch.circle_id', $existingCircleIds]);
        }

        $allCandidates = $query->asArray()->all();

        // Vaqt to'qnashuvini tekshirish (PHP'da, chunki bu soddaroq)
        $filtered = [];
        foreach ($allCandidates as $candidate) {
            if (!self::hasTimeConflict(
                $candidate['week_id'],
                $candidate['start_time'],
                $candidate['end_time'],
                $busyTimes
            )) {
                $filtered[] = $candidate;

                if (count($filtered) >= $limit) {
                    break;
                }
            }
        }

        return $filtered;
    }

    /**
     * Talabani to'garaklarga yozish
     */
    private static function enrollStudent($studentId, $userUserId, $candidates, $eduYearId, $db)
    {
        $enrolled = [];
        $currentUserId = Yii::$app->user->id ?? 0;
        $timestamp = time();

        foreach ($candidates as $schedule) {
            $scheduleId = (int)$schedule['id'];
            $circleId = (int)$schedule['circle_id'];

            // 1) Dublikat tekshiruvi (final check)
            $exists = CircleStudent::find()
                ->where([
                    'student_id' => $studentId,
                    'circle_id' => $circleId,
                    'is_deleted' => 0
                ])
                ->exists();

            if ($exists) {
                continue;
            }

            // 2) Atomar capacity yangilash
            $affected = $db->createCommand(
                'UPDATE circle_schedule
                 SET student_count = student_count + 1, updated_at = :ts
                 WHERE id = :id AND student_count < max_student_count',
                [':ts' => $timestamp, ':id' => $scheduleId]
            )->execute();

            if ($affected === 0) {
                continue;
            }

            // 3) CircleStudent yozuvi yaratish
            $cs = new CircleStudent();
            $cs->student_id = $studentId;
            $cs->student_user_id = $userUserId;
            $cs->circle_id = $circleId;
            $cs->circle_schedule_id = $scheduleId;
            $cs->edu_year_id = $eduYearId;
            $cs->semestr_type = $schedule['semestr_type'] ?? 1;
            $cs->status = CircleStudent::STATUS_RANDOM;
            $cs->created_by = $currentUserId;
            $cs->updated_by = $currentUserId;
            $cs->created_at = $timestamp;
            $cs->updated_at = $timestamp;

            if (!$cs->save()) {
                // Capacity'ni qaytarish
                $db->createCommand(
                    'UPDATE circle_schedule
                     SET student_count = student_count - 1, updated_at = :ts
                     WHERE id = :id AND student_count > 0',
                    [':ts' => $timestamp, ':id' => $scheduleId]
                )->execute();

                $errText = implode(' | ', $cs->getFirstErrors());

                // Agar dublikat xatosi bo'lsa, skip
                if (stripos($errText, 'already enrolled') !== false) {
                    continue;
                }

                // Boshqa xatolar uchun exception
                throw new \Exception('Failed to save CircleStudent: ' . $errText);
            }

            $enrolled[] = [
                'student_id' => $studentId,
                'circle_id' => $circleId,
                'schedule_id' => $scheduleId
            ];
        }

        return $enrolled;
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

            if ($model->certificate_status == 1 && !empty($model->certificate_file)) {
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
            $path = '/uploads/certificates/' .  $model->circle_id . '/' . $model->circle_schedule_id . '/';
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
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $errors[] = _e('Certificate generation error: ') . $e->getMessage();
            return simplify_errors($errors);
        }
        return simplify_errors($errors);
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
}
