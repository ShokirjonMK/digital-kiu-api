<?php

namespace api\controllers;

use Yii;
use api\resources\Direction;
use base\ResponseStatus;
use common\models\DirectionInfo;

class DirectionController extends ApiActiveController
{

    public $modelClass = 'api\resources\Direction';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Direction();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere(['status' => 1,'deleted' => 0])
            ->join('INNER JOIN', 'direction_info info', 'info.direction_id = direction.id')
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
        $model = new Direction();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Direction::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Direction successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Direction::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Direction::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Direction successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($lang, $id)
    {
        $model = Direction::find()
            ->with(['infoRelation'])
            ->join('INNER JOIN', 'direction_info info', 'info.direction_id = direction.id')
            ->andWhere(['id' => $id, 'language' => $lang])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Direction::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        // remove translations 
        DirectionInfo::deleteAll(['direction_id' => $id]);

        // remove model
        $result = Direction::findOne($id)->delete();

        if($result){
            return $this->response(1, _e('Direction succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);     
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
