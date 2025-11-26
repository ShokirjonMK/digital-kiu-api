<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\CircleSchedule;
use common\models\model\CircleStudent;
use common\models\model\EduYear;
use common\models\model\Profile;
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
        $not = Yii::$app->request->get('not', 0);

        // If not=1, return not selected students data
        if ($not == 1) {
            return $this->getNotSelectedStudents();
        }

        $model = new CircleStudent();

        $query = $model
            ->find()
            // ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = ' . $model->tableName() . '.student_user_id')
            ->leftJoin("circle_schedule", "circle_schedule.id = " . $model->tableName() . ".circle_schedule_id");

        if (isRole('student')) {
            $query->andWhere([$model->tableName() . '.student_user_id' => current_user_id()]);
        }

        $query->andWhere([$model->tableName() . '.is_deleted' => Yii::$app->request->get('is_deleted', 0)]);

        if (isRoleOnly('teacher')) {
            $query->andWhere(['circle_schedule.teacher_user_id' => current_user_id()]);
        }

        // build_id filter
        $buildId = Yii::$app->request->get('building_id');
        if (!empty($buildId)) {
            $query->andWhere(['circle_schedule.building_id' => $buildId]);
        }


        //  Filter from Profile 
        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
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
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

        $model = CircleStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        if (isset($post['is_finished'])) {
            $result = CircleStudent::isFinished($model, $post['is_finished']);
        } else {
            $result = CircleStudent::updateItem($model, $post);
        }
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

        if (isRoleOnly('teacher')) {
            if (!$model->circleSchedule || $model->circleSchedule->teacher_user_id !== current_user_id()) {
                return $this->response(0, _e('You are not authorized to view.'), null, null, ResponseStatus::FORBIDDEN);
            }
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
                $schedule->updateStudentCount();
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
        // return $this->response(0, _e('Jarayonda'), null, null, ResponseStatus::OK);

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

    public function actionCourse()
    {
        $courseId = Yii::$app->request->get('course_id');

        if (empty($courseId)) {
            return $this->response(0, _e('Course ID is required.'), null, null, ResponseStatus::BAD_REQUEST);
        }

        $result = CircleStudent::autoEnrollStudentsByCourseWorks($courseId);
        if ($result['status'] == 1) {
            return $this->response(1, _e($result['message']), $result['added'], null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e($result['message']), null, $result['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    /**
     * Get not selected students with filters and pagination support
     */
    private function getNotSelectedStudentsOld()
    {
        $request   = Yii::$app->request;
        $courseId  = $request->get('course_id');
        $eduYearId = $request->get('edu_year_id');

        // 1) edu_year_id ni aniqlash (fallback sifatida oxirgi aktiv yilni olish)
        if (empty($eduYearId)) {
            $lastEduYearId = EduYear::find()
                ->select('id')
                ->where(['is_deleted' => 0, 'status' => 1])
                ->orderBy(['id' => SORT_DESC])
                ->limit(1)
                ->scalar();

            if (!$lastEduYearId) {
                return $this->response(0, _e('Active edu year not found.'), null, null, ResponseStatus::BAD_REQUEST);
            }
            $eduYearId = (int)$lastEduYearId;
        } else {
            $eduYearId = (int)$eduYearId;
        }

        // 2) Asosiy query
        $query = Student::find()
            ->alias('s')
            ->innerJoin(Profile::tableName() . ' p', 'p.user_id = s.user_id')
            ->where([
                's.status'     => 10,
                's.is_deleted' => 0,
            ]);

        // 3) Kurs bo‘yicha filter (xato shart to‘g‘rilandi)
        if (!empty($courseId)) {
            $query->andWhere(['s.course_id' => (int)$courseId]);
        }

        // 4) Shu o‘quv yilida circle_student yozuvlari 2 tadan kam bo‘lishi sharti
        //    (agar 0 ta kerak bo‘lsa, ':maxCount' ni 1 ga tushiring)
        $maxCount = 2;
        $query->andWhere(new \yii\db\Expression(
            '(SELECT COUNT(*) FROM {{%circle_student}} cs 
          WHERE cs.student_id = s.id 
            AND cs.edu_year_id = :eduYearId 
            AND cs.is_deleted = 0) < :maxCount'
        ), [
            ':eduYearId' => $eduYearId,
            ':maxCount'  => $maxCount,
        ]);

        // 5) Profile bo‘yicha aniq filterlar: ?filter={"region_id":8,"gender":1}
        $profile = new Profile();
        $filter  = $request->get('filter');
        if (!empty($filter)) {
            $filterArr = json_decode(str_replace("'", "", $filter), true);
            if (is_array($filterArr)) {
                foreach ($filterArr as $attribute => $value) {
                    if ($profile->hasAttribute($attribute)) {
                        $query->andFilterWhere(['p.' . $attribute => $value]);
                    }
                }
            }
        }

        // 6) Profile bo‘yicha LIKE qidiruvlar: ?filter-like={"last_name":"Ali"}
        $filterLike = $request->get('filter-like');
        if (!empty($filterLike)) {
            $filterLikeArr = json_decode(str_replace("'", "", $filterLike), true);
            if (is_array($filterLikeArr)) {
                foreach ($filterLikeArr as $attribute => $word) {
                    if ($profile->hasAttribute($attribute) && $word !== '' && $word !== null) {
                        // Yii2 LIKE %word% ni o‘zi qo‘llaydi (escape bilan)
                        $query->andFilterWhere(['like', 'p.' . $attribute, $word]);
                    }
                }
            }
        }

        // 7) Umumiy filter va sortlash
        $model = new Student();
        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        dd($query->createCommand()->rawSql);

        // 8) Ma’lumotni olish va javob
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    private function getNotSelectedStudents()
    {
        $request   = Yii::$app->request;
        $courseId  = (int)$request->get('course_id');
        $eduYearId = (int)$request->get('edu_year_id');

        // 1) edu_year_id aniqlash (fallback: oxirgi aktiv yil)
        if (empty($eduYearId)) {
            $eduYearId = (int)EduYear::find()
                ->select('id')
                ->where(['is_deleted' => 0, 'status' => 1])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();

            if (!$eduYearId) {
                return $this->response(0, _e('Active edu year not found.'), null, null, ResponseStatus::BAD_REQUEST);
            }
        }

        // 2) Shu o‘quv yilida har bir student uchun nechta circle_student borligini hisoblaydigan subquery
        $csAgg = (new \yii\db\Query())
            ->select([
                'student_id',
                'cnt' => new \yii\db\Expression('COUNT(*)'),
            ])
            ->from('{{%circle_student}} cs')
            ->where([
                'cs.edu_year_id' => $eduYearId,
                'cs.is_deleted'  => 0,
            ])
            ->groupBy('cs.student_id');

        // 3) Asosiy query: student + profile
        $query = Student::find()
            ->alias('s')
            ->innerJoin(Profile::tableName() . ' p', 'p.user_id = s.user_id')
            // Subquery bilan LEFT JOIN: yo'q bo‘lsa NULL cnt, bo‘lsa real COUNT
            ->leftJoin(['csa' => $csAgg], 'csa.student_id = s.id')
            ->where([
                's.status'     => 10,
                's.is_deleted' => 0,
            ])
            ->andWhere(['s.edu_form_id' => 1]);

        // 4) Kurs bo‘yicha filter (ixtiyoriy)
        if (!empty($courseId)) {
            $query->andWhere(['s.course_id' => $courseId]);
        }

        // 5) Joriy yilda circle_student < 2 bo‘lganlar (0 yoki 1)
        $query->andWhere([
            'or',
            ['csa.cnt' => null],              // umuman yo‘q (0 ta)
            ['<', 'csa.cnt', 2],              // 1 ta
        ]);

        // 6) Profile bo‘yicha exact filterlar: ?filter={"region_id":8,"gender":1}
        $profile    = new Profile();
        $filterJson = $request->get('filter');
        if (!empty($filterJson)) {
            $filterArr = json_decode($filterJson, true);
            if (is_array($filterArr)) {
                foreach ($filterArr as $attr => $val) {
                    if ($profile->hasAttribute($attr)) {
                        $query->andFilterWhere(['p.' . $attr => $val]);
                    }
                }
            }
        }

        // 7) Profile bo‘yicha LIKE qidiruv: ?filter-like={"last_name":"Ali"}
        $filterLikeJson = $request->get('filter-like');
        if (!empty($filterLikeJson)) {
            $filterLikeArr = json_decode($filterLikeJson, true);
            if (is_array($filterLikeArr)) {
                foreach ($filterLikeArr as $attr => $word) {
                    if ($profile->hasAttribute($attr) && $word !== '' && $word !== null) {
                        $query->andFilterWhere(['like', 'p.' . $attr, $word]);
                    }
                }
            }
        }

        // 8) Umumiy filter va sort (sizning helperlaringiz)
        $model = new Student();
        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        // ⚠️ Ishlab chiqarishda dd() qo‘ymang — u skriptni to‘xtatadi.
        if (Yii::$app->request->get('sql')) {
            dd($query->createCommand()->rawSql);
        }
        // dd($query->createCommand()->rawSql);

        // 9) Ma’lumotni olish va javob
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }
}
