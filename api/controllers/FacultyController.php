<?php

namespace api\controllers;

use common\models\model\Faculty;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use yii\web\ForbiddenHttpException;

class FacultyController extends ApiController
{
    public $modelClass = 'api\resources\Faculty';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {


        $model = new Faculty();

        $query = $model->find()
            ->andFilterWhere(['status' => 1,'is_deleted' => 0])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);
        
        // sort
        $query = $this->sort($query);
        
        // data
        $data =  $this->getData($query);
        
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Faculty();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Faculty::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Job successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Faculty::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Faculty::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Job successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($lang, $id)
    {
        $model = Faculty::find()
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
        $model = Faculty::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        // remove translations 
        JobInfo::deleteAll(['job_id' => $id]);

        // remove model
        $result = Faculty::findOne($id)->delete();

        if($result){
            return $this->response(1, _e('Job succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);     
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
    



    

    

}
