<?php

namespace api\controllers;

use Yii;

use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Semestr;
use common\models\model\Student;
use common\models\model\StudentTimeTable;
use common\models\model\TimeTable;

class  StudentTimeTableController extends ApiActiveController
{

    public $modelClass = 'api\resources\StudentTimeTable';

    public function actions()
    {
        return [];
    }
    public $table_name = 'student_time_table';
    public $controller_name = 'StudentTimeTable';

    public function actionIndex($lang)
    {
        $model = new StudentTimeTable();
        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        $student = Student::findOne(['user_id' => Current_user_id()]);

        $semester = Semestr::findOne(Yii::$app->request->get('semester_id'));

        if ($semester) {
            $eduSemestr = EduSemestr::findOne($semester->id);
        } else {
            $eduSemestr = EduSemestr::find()->where(['status' => 1])->one();
        }
        if ($eduSemestr) {
            $timeTablesIds = TimeTable::find()
                ->select('id')
                ->where(['edu_semester_id' => $eduSemestr->id])
                ->all();
            if ($timeTablesIds) {
                $query->andWhere(['in', 'time_table_id', $timeTablesIds]);
            }
        }

        if (isset($student)) {
            $query->andWhere(['student_id' => $student->id]);
        } 

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new StudentTimeTable();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentTimeTable::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentTimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model_new = new StudentTimeTable();
        $post = Yii::$app->request->post();
        $this->load($model_new, $post);

        $result = StudentTimeTable::updateItem($model_new, $post, $model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentTimeTable::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentTimeTable::findOne(['id' => $id]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = StudentTimeTable::findOne($id)->delete();

        if ($result) {
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
