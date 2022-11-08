<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\ExamControlStudent;
use common\models\model\Faculty;
use Yii;
use yii\rest\ActiveController;

class ExamControlStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControlStudent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_control_student';
    public $controller_name = 'ExamControlStudent';


    public function actionIndex($lang)
    {
        $model = new ExamControlStudent();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            // ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        // is Self 
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query->andFilterWhere([
                'faculty_id' => $t['UserAccess']->table_id
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
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
        $model = new ExamControlStudent();
        $post = Yii::$app->request->post();
        $data = [];
        if (isRole('student')) {
            if (isset($post['exam_control_id'])) $data['exam_control_id'] = $post['exam_control_id'];
            if (isset($post['upload2_file'])) $data['upload2_file'] = $post['upload2_file'];
            if (isset($post['upload_file'])) $data['upload_file'] = $post['upload_file'];
            if (isset($post['answer2'])) $data['answer2'] = $post['answer2'];
            if (isset($post['answer'])) $data['answer'] = $post['answer'];

            $this->load($model, $data);
            $result = ExamControlStudent::createItem($model, $data);
        } else {
            // if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
            if (isset($post['upload2_file'])) unset($post['upload2_file']);
            if (isset($post['upload_file'])) unset($post['upload_file']);
            if (isset($post['answer2'])) unset($post['answer2']);
            if (isset($post['answer'])) unset($post['answer']);
            if (isset($post['main_ball'])) unset($post['main_ball']);

            $this->load($model, $post);
            $result = ExamControlStudent::createItem($model, $post);
        }
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamControlStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $data = [];
        $post = Yii::$app->request->post();

        if (isRole('student')) {
            if ($model->student_id != $this->studeny()) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
            if (isset($post['exam_control_id'])) $data['exam_control_id'] = $post['exam_control_id'];
            if (isset($post['upload2_file'])) $data['upload2_file'] = $post['upload2_file'];
            if (isset($post['upload_file'])) $data['upload_file'] = $post['upload_file'];
            if (isset($post['answer2'])) $data['answer2'] = $post['answer2'];
            if (isset($post['answer'])) $data['answer'] = $post['answer'];

            $this->load($model, $data);
            $result = ExamControlStudent::createItem($model, $data);
        } else {
            if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
            if (isset($post['upload2_file'])) unset($post['upload2_file']);
            if (isset($post['upload_file'])) unset($post['upload_file']);
            if (isset($post['answer2'])) unset($post['answer2']);
            if (isset($post['answer'])) unset($post['answer']);
            if (isset($post['main_ball'])) unset($post['main_ball']);

            $this->load($model, $post);
            $result = ExamControlStudent::createItem($model, $post);
        }

        $this->load($model, $post);
        $result = ExamControlStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamControlStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControlStudent::find()
            // ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        $model->delete();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
