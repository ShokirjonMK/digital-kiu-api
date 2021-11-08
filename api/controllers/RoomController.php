<?php

namespace api\controllers;

use common\models\model\Room;
use common\models\model\Translate;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Semestr;
use common\models\model\TimeTable;

class RoomController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'room';
    public $controller_name = 'Room';


    public function actionFree($lang)
    {
        
        $semester_id = Yii::$app->request->get('semester_id');

        $semester = Semestr::findOne($semester_id);

        if (!isset($semester)) {
            return $this->response(0, _e('Semester not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $type = $semester->type;

        $semester_ids = Semestr::find()->select('id')->where(['type' => $type]);

        $roomIds =  TimeTable::find()
            ->select('room_id')
            ->where([
                'para_id' => Yii::$app->request->get('para_id'),
                'edu_year_id' => Yii::$app->request->get('edu_year_id'),
                'week_id' => Yii::$app->request->get('week_id')

            ])->andWhere(['in', 'semester_id', $semester_ids]);


        $model = new Room();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        if (isset($roomIds)) {
            $query->andFilterWhere(['not in', 'id', $roomIds]);
        }

        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex($lang)
    {
        $model = new Room();

        $query = $model->find()
            ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            // ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['tr.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Room();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Room::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Room::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Room::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Room::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Room::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }







}
