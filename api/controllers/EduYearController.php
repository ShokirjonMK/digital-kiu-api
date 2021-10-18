<?php

namespace api\controllers;

use common\models\model\EduYear;
use common\models\model\Room;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;

class EduYearController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduYear';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new EduYear();

        $query = $model->find()
            ->andWhere(['status' => 1,'is_deleted' => 0])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new EduYear();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduYear::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Job successfully created.'), $model, null, ResponseStatus::CREATED);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduYear::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduYear::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Job successfully updated.'), $model, null, ResponseStatus::OK);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduYear::find()
            ->with(['infoRelation'])
            ->join('INNER JOIN', 'job_info info', 'info.job_id = job.id')
            ->andWhere(['id' => $id, 'language' => $lang])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = EduYear::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = EduYear::findOne($id);

        if($result){
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('Room succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }








}
