<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\CircleSchedule;
use common\models\model\CircleStudent;
use common\models\model\EduYear;
use common\models\model\Student;
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

    /**
     * Course bo'yicha studentlarni avtomatik circle_student ga qo'shish
     * POST course_id
     */
    public function actionCourse1($lang)
    {
        $course_id = Yii::$app->request->get('course_id');

        if (empty($course_id)) {
            return $this->response(0, _e('Course ID is required.'), null, null, ResponseStatus::BAD_REQUEST);
        }

        $courseId = (int)$course_id;

        $result = CircleStudent::autoEnrollStudentsByCourse($courseId);

        if (!is_array($result)) {
            return $this->response(1, _e('Students successfully enrolled to circles.'), $result, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    /**
     * Course bo'yicha studentlarni avtomatik circle_student ga qo'shish
     * Get course_id
     */
    public function actionCourse2()
    {
        $courseId = Yii::$app->request->get('course_id');

        if (empty($courseId)) {
            return $this->response(0, _e('Course ID is required.'), null, null, ResponseStatus::BAD_REQUEST);
        }

        $eduYearId = EduYear::find()
            ->where(['is_deleted' => 0, 'status' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one()
            ->id;

        $currentMonth = (int)date('n');
        $semesterType = ($currentMonth >= 9 || $currentMonth <= 1) ? 1 : 2;
        $semesterType = 1;

        $students = Student::find()
            ->where(['course_id' => $courseId, 'status' => 10])
            ->all();

        $added = [];
        foreach ($students as $student) {
            // Talabaning shu yildagi circle_student count
            $count = CircleStudent::find()
                ->where([
                    'student_id' => $student->id,
                    'edu_year_id' => $eduYearId,
                    'is_deleted' => 0
                ])
                ->count();

            if ($count >= 2) {
                continue; // 2 tadan ortiq bo‘lsa o‘tkazib yuboramiz
            }

            // Qancha qo‘shish kerakligini hisoblaymiz
            $need = 2 - $count;

            // Studentning allaqachon yozilgan circle_id larini olish
            $takenCircleIds = CircleStudent::find()
                ->alias('cs')
                ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                ->where([
                    'cs.student_id' => $student->id,
                    'cs.edu_year_id' => $eduYearId,
                    'cs.is_deleted' => 0
                ])
                ->select('sch.circle_id')
                ->column();

            // 2️⃣ Mos circle_schedule larni topish
            $schedules = CircleSchedule::find()
                ->where(['edu_year_id' => $eduYearId])
                ->andWhere(['semestr_type' => $semesterType])
                ->andWhere(['<', 'student_count', 30])
                ->andFilterWhere(['not in', 'circle_id', $takenCircleIds])
                ->orderBy(new \yii\db\Expression('RAND()')) // random taqsimlash
                ->limit($need)
                ->all();

            foreach ($schedules as $schedule) {
                $circleStudent = new CircleStudent();
                $circleStudent->student_id = $student->id;
                $circleStudent->student_user_id = $student->user_id;
                $circleStudent->circle_id = $schedule->circle_id;
                $circleStudent->circle_schedule_id = $schedule->id;
                $circleStudent->edu_year_id = $eduYearId;
                if ($circleStudent->save()) {
                    // student_count ni update qilish
                    $schedule->student_count = CircleStudent::find()
                        ->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])
                        ->count();
                    $schedule->save(false);

                    $added[] = [
                        'student_id' => $student->id,
                        'circle_id' => $schedule->circle_id,
                        'schedule_id' => $schedule->id
                    ];
                }
            }
        }

        return $this->asJson([
            'status' => 1,
            'message' => 'Students enrolled successfully',
            'added' => $added
        ]);
    }

    public function actionCourse3()
    {
        $courseId = Yii::$app->request->get('course_id');
        if (!$courseId) {
            return $this->asJson([
                'status' => 0,
                'message' => 'course_id is required'
            ]);
        }

        $eduYearId = EduYear::find()
            ->where(['is_deleted' => 0, 'status' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one()
            ->id;

        $students = Student::find()
            ->where(['course_id' => $courseId, 'status' => 10])
            ->all();

        $added = [];
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        try {
            foreach ($students as $student) {

                // Studentning shu yilgi yozuvlari
                $existing = CircleStudent::find()
                    ->alias('cs')
                    ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                    ->where([
                        'cs.student_id' => $student->id,
                        'cs.edu_year_id' => $eduYearId,
                        'cs.is_deleted' => 0
                    ])
                    ->select(['sch.circle_id', 'cs.circle_schedule_id'])
                    ->asArray()
                    ->all();

                $count = count($existing);
                if ($count >= 2) {
                    continue; // to‘liq
                }

                $need = 2 - $count;
                $takenCircles = array_column($existing, 'circle_id');
                $takenSchedules = array_column($existing, 'circle_schedule_id');

                // Eng kam studentli schedule larni olish
                $schedules = CircleSchedule::find()
                    ->where([
                        'edu_year_id' => $eduYearId,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                    ->andWhere(['<', 'student_count', new \yii\db\Expression('max_student_count')])
                    ->andFilterWhere(['not in', 'circle_id', $takenCircles])
                    ->andFilterWhere(['not in', 'id', $takenSchedules])
                    ->orderBy(['student_count' => SORT_ASC]) // eng bo‘shlarini olish
                    ->limit($need * 3) // biroz ko‘proq olib, keyin filterlaymiz
                    ->all();

                $chosen = [];
                foreach ($schedules as $schedule) {
                    if (count($chosen) >= $need) {
                        break;
                    }
                    // Talaba shu circle_id ga yozilmaganini tekshirish
                    if (in_array($schedule->circle_id, $takenCircles)) {
                        continue;
                    }
                    $chosen[] = $schedule;
                    $takenCircles[] = $schedule->circle_id; // update qilamiz
                }

                foreach ($chosen as $schedule) {
                    $circleStudent = new CircleStudent();
                    $circleStudent->student_id = $student->id;
                    $circleStudent->student_user_id = $student->user_id;
                    $circleStudent->circle_id = $schedule->circle_id;
                    $circleStudent->circle_schedule_id = $schedule->id;
                    $circleStudent->edu_year_id = $eduYearId;
                    $circleStudent->created_by = Yii::$app->user->id ?? 0;
                    $circleStudent->updated_by = Yii::$app->user->id ?? 0;
                    $circleStudent->created_at = time();
                    $circleStudent->updated_at = time();

                    if (!$circleStudent->save()) {
                        throw new \Exception("CircleStudent save error: " . json_encode($circleStudent->errors));
                    }

                    // student_count ni yangilash
                    $schedule->updateCounters(['student_count' => 1]);

                    $added[] = [
                        'student_id' => $student->id,
                        'circle_id' => $schedule->circle_id,
                        'schedule_id' => $schedule->id
                    ];
                }
            }

            $transaction->commit();
            return $this->asJson([
                'status' => 1,
                'message' => 'Students enrolled successfully',
                'added' => $added
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->asJson([
                'status' => 0,
                'message' => 'Error while enrolling students',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function actionCourse()
    {
        $courseId = Yii::$app->request->get('course_id');

        if (empty($courseId)) {
            return $this->response(0, _e('Course ID is required.'), null, null, ResponseStatus::BAD_REQUEST);
        }

        // vd('courseId: ' . $courseId);

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
            // vd('talabalar: ' . count($students));

            foreach ($students as $student) {

                // 2️⃣ Studentning mavjud circle_id larini olish
                $takenCircleIds = CircleStudent::find()
                    ->alias('cs')
                    ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
                    ->where([
                        'cs.student_id' => $student->id,
                        'cs.edu_year_id' => $eduYearId,
                        'cs.is_deleted' => 0
                    ])
                    ->select('sch.circle_id')
                    ->column();

                // Hali nechta kerak?
                $currentCount = count($takenCircleIds);
                // vd('currentCount: ' . $currentCount);
                $need = 2 - $currentCount;
                // vd('need: ' . $need);
                if ($need <= 0) {
                    continue;
                }

                // vd('takenCircleIds: ' . count($takenCircleIds));

                // 3️⃣ Mos bo‘sh circle_schedule larini olish
                // Har bir circle_id bo'yicha faqat bittadan schedule olish
                $schedules = CircleSchedule::find()
                    ->where([
                        'edu_year_id' => $eduYearId,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                    ->andWhere(['<', 'student_count', new \yii\db\Expression('max_student_count')])
                    ->andFilterWhere(['not in', 'circle_id', $takenCircleIds])
                    ->orderBy([
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

                // vd('schedules: ' . count($schedules));

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

                    if (!$circleStudent->save()) {
                        throw new \Exception(json_encode($circleStudent->errors));
                    }

                    // student_count ni yangilash
                    $schedule->updateCounters(['student_count' => 1]);

                    $added[] = [
                        'student_id' => $student->id,
                        'circle_id' => $schedule->circle_id,
                        'schedule_id' => $schedule->id
                    ];
                }
            }

            $transaction->commit();
            return $this->asJson([
                'status' => 1,
                'message' => 'Students filled successfully',
                'added' => $added
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->asJson([
                'status' => 0,
                'message' => 'Error',
                'error' => $e->getMessage()
            ]);
        }
    }
}
