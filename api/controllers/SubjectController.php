<?php

namespace api\controllers;

use Yii;
use api\resources\Subject;
use base\ResponseStatus;
use common\models\SubjectInfo;

class SubjectController extends ApiActiveController
{
    public $modelClass = 'api\resources\Subject';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Subject();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere(['status' => 1,'deleted' => 0])
            ->join('INNER JOIN', 'subject_info info', 'info.subject_id = subject.id')
            ->andWhere(['language' => Yii::$app->request->get('lang')])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);
        
        // sort
        $query = $this->sort($query);
        
        // data
        $data =  $this->getData($query);
        
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Subject();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Subject::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Subject successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Subject::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Subject::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Subject successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($lang, $id)
    {
        $model = Subject::find()
            ->with(['infoRelation'])
            ->join('INNER JOIN', 'subject_info info', 'info.subject_id = subject.id')
            ->andWhere(['id' => $id, 'language' => $lang])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Subject::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        // remove translations 
        SubjectInfo::deleteAll(['subject_id' => $id]);

        // remove model
        $result = Subject::findOne($id)->delete();

        if($result){
            return $this->response(1, _e('Subject succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);     
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
    



    

    

}
