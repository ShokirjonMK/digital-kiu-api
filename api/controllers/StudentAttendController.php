<?php

namespace api\controllers;

use common\models\model\StudentAttend;
use Yii;
use base\ResponseStatus;

class StudentAttendController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentAttend';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_attend';
    public $controller_name = 'StudentAttend';

    public function actionIndex($lang)
    {
        $model = new StudentAttend();

        $query = $model->find()
            // ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
        ;
        $group_by = Yii::$app->request->get('group_by');
        if (isset($group_by)) {
            if (($group_by[0] == "'") && ($group_by[strlen($group_by) - 1] == "'")) {
                $group_by =  substr($group_by, 1, -1);
            }
            // $query = $query->select([$this->table_name . '.*', 'COUNT(' . $this->table_name . '.id) AS countlike']);
            // ->join('LEFT JOIN', Likes::tableName(), 'videos.id=likes.video_id')
            // ->groupBy('videos.id')
            // ->limit(10);

            $query = $query->groupBy(((array)json_decode($group_by)));
            // $query = $query->orderBy(['countlike' => SORT_DESC]);
            $query = $query->orderBy(['COUNT(' . $this->table_name . '.id)' => SORT_DESC]);
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
        $model = new StudentAttend();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentAttend::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentAttend::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = StudentAttend::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentAttend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentAttend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
