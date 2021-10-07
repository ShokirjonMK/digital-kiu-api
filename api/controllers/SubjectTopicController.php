<?php

namespace api\controllers;

use Yii;
use api\resources\SubjectTopic;
use base\ResponseStatus;
use common\models\SubjectTopicInfo;

class SubjectTopicController extends ApiActiveController
{
    public $modelClass = 'api\resources\SubjectTopic';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new SubjectTopic();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere(['status' => 1,'deleted' => 0])
            ->join('INNER JOIN', 'subject_topic_info info', 'info.subject_topic_id = subject_topic.id')
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
        $model = new SubjectTopic();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = SubjectTopic::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Subject topic successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = SubjectTopic::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = SubjectTopic::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Subject topic successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($lang, $id)
    {
        $model = SubjectTopic::find()
            ->with(['infoRelation'])
            ->join('INNER JOIN', 'subject_topic_info info', 'info.subject_topic_id = subject_topic.id')
            ->andWhere(['id' => $id, 'language' => $lang])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = SubjectTopic::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        // remove translations 
        SubjectTopicInfo::deleteAll(['subject_topic_id' => $id]);

        // remove model
        $result = SubjectTopic::findOne($id)->delete();

        if($result){
            return $this->response(1, _e('Subject topic succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);     
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
    



    

    

}
