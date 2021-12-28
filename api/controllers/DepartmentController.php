<?php

namespace api\controllers;

use Yii;
use api\resources\Department;
use base\ResponseStatus;
use common\models\DepartmentInfo;

class DepartmentController extends ApiActiveController
{
    public $modelClass = 'api\resources\Department';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Department();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere(['deleted' => 0])
            ->join('INNER JOIN', 'department_info info', 'info.department_id = department.id')
            ->andWhere(['language' => Yii::$app->request->get('lang')])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);

        //filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        
        // data
        $data =  $this->getData($query);
        
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Department();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Department::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Department successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Department::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Department::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Department successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($lang, $id)
    {
        $model = Department::find()
            ->with(['infoRelation'])
            ->join('INNER JOIN', 'department_info info', 'info.department_id = department.id')
            ->andWhere(['id' => $id, 'language' => $lang])
            ->one();
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Department::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);  
        }

        // remove translations 
        DepartmentInfo::deleteAll(['department_id' => $id]);

        // remove model
        $result = Department::findOne($id)->delete();

        if($result){
            return $this->response(1, _e('Department succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);     
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        
    }  

}
