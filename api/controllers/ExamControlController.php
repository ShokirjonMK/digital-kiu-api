<?php

namespace api\controllers;


use Yii;
use base\ResponseStatus;
use common\models\model\ExamControl;
use common\models\model\Faculty;
use common\models\model\Student;

class ExamControlController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControl';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_control';
    public $controller_name = 'ExamControl';


    public function actionIndex($lang)
    {
        $model = new ExamControl();
        $student = Student::findOne(['user_id' => Current_user_id()]);

        $query = $model->find();

        $query = $query->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        $statuses = json_decode(str_replace("'", "", Yii::$app->request->get('statuses')));

        if ($statuses) {
            $query->andFilterWhere([
                'in', $this->table_name . '.status',
                $statuses
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);
        // sort
        $query = $this->sort($query);
        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new ExamControl();
        $post = Yii::$app->request->post();
        // $post['duration'] =  strtotime($post['duration']);

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['duration2'])) {
            $post['duration2'] =  str_replace("'", "", $post['duration2']);
            $post['duration2'] =  str_replace('"', "", $post['duration2']);
            $duration2 = explode(":", $post['duration2']);
            $hours = isset($duration2[0]) ? $duration2[0] : 0;
            $min = isset($duration2[1]) ? $duration2[1] : 0;
            $post['duration2'] = (int)$hours * 3600 + (int)$min * 60;
        }

        $this->load($model, $post);
        if (isset($post['start'])) {
            $model['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish'] = strtotime($post['finish']);
        }
        if (isset($post['start2'])) {
            $model['start2'] = strtotime($post['start2']);
        }
        if (isset($post['finish2'])) {
            $model['finish2'] = strtotime($post['finish2']);
        }

        $result = ExamControl::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamControl::findOne($id);

        // /*  is Self  */
        // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     if ($model->faculty_id != $t['UserAccess']->table_id) {
        //         return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        //     }
        // } elseif ($t['status'] == 2) {
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        // }
        // /*  is Self  */

        $post = Yii::$app->request->post();


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }
        if (isset($post['duration2'])) {
            $post['duration2'] =  str_replace("'", "", $post['duration2']);
            $post['duration2'] =  str_replace('"', "", $post['duration2']);
            $duration2 = explode(":", $post['duration2']);
            $hours = isset($duration2[0]) ? $duration2[0] : 0;
            $min = isset($duration2[1]) ? $duration2[1] : 0;
            $post['duration2'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['appeal2_at'])) {
            $post['appeal2_at'] = strtotime($post['appeal2_at']);
        }

        if (isset($post['appeal_at'])) {
            $post['appeal_at'] = strtotime($post['appeal_at']);
        }

        if (isset($post['status'])) {
            if ($model->status == 2 && $post['status'] == 2)
                unset($post['status']);
        }

        if (isset($post['status2'])) {
            if ($model->status2 == 2 && $post['status2'] == 2)
                unset($post['status2']);
        }

        $this->load($model, $post);
        if (isset($post['start'])) {
            $model['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish'] = strtotime($post['finish']);
        }

        if (isset($post['start2'])) {
            $model['start2'] = strtotime($post['start2']);
        }
        if (isset($post['finish2'])) {
            $model['finish2'] = strtotime($post['finish2']);
        }

        $result = ExamControl::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamControl::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControl::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            if ($model->timeTable->teacher_user_id != current_user_id() && isRole('teacher')) {
                $errors[] = _e('This is not your timeTable');

                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not your timeTable'), ResponseStatus::BAD_REQUEST);
            }
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
