<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\Circle;
use common\models\model\CircleSchedule;
use common\models\model\CircleStudent;
use common\models\model\Student;
use Yii;

class CircleController extends ApiActiveController
{
    public $modelClass = 'api\resources\Circle';

    public function actions()
    {
        return [];
    }

    public $controller_name = 'Circle';

    public function actionIndex($lang)
    {
        $model = new Circle();

        $query = $model
            ->find()
            ->leftJoin('translate tr', "tr.model_id = {$model->tableName()}.id and tr.table_name = '{$model->tableName()}'")
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);


        // if (isRole('teacher') && !isRole('admin')) {
        //     $query->leftJoin('circle_schedule cs', "cs.circle_id = {$model->tableName()}.id and cs.is_deleted = 0");
        //     $query->andWhere(['cs.teacher_user_id' => current_user_id()]);
        // }

        if (isRole('teacher') && !isRole('admin')) {
            $table = $model->tableName();
            $query->leftJoin(
                'circle_schedule cs',
                "cs.circle_id = {$table}.id AND cs.is_deleted = 0 AND cs.teacher_user_id = :teacher_id",
                [':teacher_id' => current_user_id()]
            );
            $query->andWhere(['cs.teacher_user_id' => current_user_id()]);
        }


        $query->andWhere([$model->tableName() . '.is_deleted' => Yii::$app->request->get('is_deleted', 0)]);



        $query->groupBy($model->tableName() . '.id');

        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Circle();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Circle::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        if ($id == 0) {
            $post = Yii::$app->request->post();
            $finishStatus = $post['finished_status'] ?? null;

            if ($finishStatus === null) {
                return $this->response(0, _e('Finished status is required.'), null, null, ResponseStatus::BAD_REQUEST);
            }

            // Faqat kerakli yozuvlar yangilanishi kerak
            $updated = Circle::updateAll(['finished_status' => $finishStatus], ['!=', 'finished_status', $finishStatus]);

            // $updated = Circle::updateAll(['finished_status' => $finishStatus]);
            if ($updated) {
                return $this->response(1, _e('Success.'), null, null, ResponseStatus::OK);
            }
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        // id = 0

        $model = Circle::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Circle::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Circle::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Circle::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionStatistic($lang)
    {
        $eduYearId = Yii::$app->request->get('edu_year_id');
        $courseId  = Yii::$app->request->get('course_id');
        $cacheKey = 'circle_statistic:' . ($eduYearId ?? 'all') . ':' . ($courseId ?? 'all');

        $cached = Yii::$app->cache->get($cacheKey);
        if ($cached !== false) {
            return $this->response(1, _e('Success'), $cached, null, ResponseStatus::OK);
        }

        // 1) Jami to'garaklar soni (circle)
        $totalCircles = Circle::find()
            ->andWhere(['is_deleted' => 0])
            ->count();

        // 2) Jami guruhlar soni (circle_schedule) [edu_year_id bo'yicha filter]
        $scheduleQuery = CircleSchedule::find()
            ->andWhere(['is_deleted' => 0]);
        if (!empty($eduYearId)) {
            $scheduleQuery->andWhere(['edu_year_id' => (int)$eduYearId]);
        }
        $totalSchedules = $scheduleQuery->count();

        // 3) Tanlagan talabalar (circle_student)
        $selectedByCourse = null;
        if (!empty($courseId)) {
            // course_id berilgan bo'lsa – umumiy son
            $selectedQuery = CircleStudent::find()
                ->alias('cs')
                ->innerJoin('student s', 's.id = cs.student_id')
                ->andWhere(['cs.is_deleted' => 0, 's.is_deleted' => 0, 's.course_id' => (int)$courseId]);
            if (!empty($eduYearId)) {
                $selectedQuery->andWhere(['cs.edu_year_id' => (int)$eduYearId]);
            }
            $totalSelectedStudents = $selectedQuery->count();
        } else {
            // course_id berilmagan bo'lsa – course bo'yicha guruhlab sanash
            $selectedAgg = CircleStudent::find()
                ->alias('cs')
                ->select(['s.course_id', 'cnt' => 'COUNT(*)'])
                ->innerJoin('student s', 's.id = cs.student_id')
                ->andWhere(['cs.is_deleted' => 0, 's.is_deleted' => 0])
                ->andFilterWhere(['cs.edu_year_id' => $eduYearId])
                ->groupBy('s.course_id')
                ->asArray()
                ->all();
            $totalSelectedStudents = null;
            $selectedByCourse = $selectedAgg; // [ ['course_id'=>X,'cnt'=>N], ... ]
        }

        // 4) Jami tanlamagan talabalar (shu yilda 2 tadan kam tanlaganlar)
        //    Student.status = 10, is_deleted = 0
        $subQuery = '(SELECT COUNT(*) FROM {{%circle_student}} cs WHERE cs.student_id = s.id AND cs.is_deleted = 0'
            . (!empty($eduYearId) ? ' AND cs.edu_year_id = :eduYearId' : '') . ') < :maxCount';

        $params = [
            ':maxCount' => 2,
        ];
        if (!empty($eduYearId)) {
            $params[':eduYearId'] = (int)$eduYearId;
        }

        $notSelectedByCourse = null;
        if (!empty($courseId)) {
            // course_id berilgan – umumiy son
            $notSelectedQuery = Student::find()
                ->alias('s')
                ->where(['s.status' => 10, 's.is_deleted' => 0, 's.course_id' => (int)$courseId])
                ->andWhere(new \yii\db\Expression($subQuery), $params);
            $totalNotSelectedStudents = $notSelectedQuery->count();
        } else {
            // course_id berilmagan – course bo'yicha guruhlab sanash
            $notSelectedAgg = Student::find()
                ->alias('s')
                ->select(['s.course_id', 'cnt' => 'COUNT(*)'])
                ->where(['s.status' => 10, 's.is_deleted' => 0])
                ->andWhere(new \yii\db\Expression($subQuery), $params)
                ->groupBy('s.course_id')
                ->asArray()
                ->all();
            $totalNotSelectedStudents = null;
            $notSelectedByCourse = $notSelectedAgg;
        }

        $data = [
            'total_circles' => (int)$totalCircles,
            'total_schedules' => (int)$totalSchedules,
        ];
        if (!empty($courseId)) {
            $data['total_selected_students'] = (int)$totalSelectedStudents;
            $data['total_not_selected_students'] = (int)$totalNotSelectedStudents;
        } else {
            $data['total_selected_students'] = $selectedByCourse;
            $data['total_not_selected_students'] = $notSelectedByCourse;
        }

        // Cache for 60 minutes (3600 seconds)
        Yii::$app->cache->set($cacheKey, $data, 3600);

        return $this->response(1, _e('Success'), $data, null, ResponseStatus::OK);
    }

    // /api/circles/{id}/schedules [GET, POST]
    public function actionSchedules($lang, $id)
    {
        if (Yii::$app->request->isGet) {
            $query = CircleSchedule::find()->where(['circle_id' => $id, 'is_deleted' => 0]);
            $query = $this->filterAll($query, new CircleSchedule());
            $query = $this->sort($query);
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (Yii::$app->request->isPost) {
            $model = new CircleSchedule();
            $post = Yii::$app->request->post();
            $post['circle_id'] = $id;
            $this->load($model, $post);
            $result = CircleSchedule::createItem($model, $post);
            if (!is_array($result)) {
                return $this->response(1, _e('Schedule successfully created.'), $model, null, ResponseStatus::CREATED);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        return $this->response(0, _e('Bad request.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
