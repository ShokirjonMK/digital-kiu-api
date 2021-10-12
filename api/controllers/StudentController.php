<?php

namespace api\controllers;

use Yii;
use api\resources\StudentUser;
use api\resources\Profile;
use common\models\Student as CommonStudent;
use base\ResponseStatus;

class StudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentUser';

    public function actions()
    {
        return [];
    }

    public function actionIndex()
    {
        $model = new StudentUser();

        $query = $model->find()
            ->with(['profile'])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->andWhere(['in', 'auth_assignment.item_name', StudentUser::$roleList])
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('q')]);
        
        // sort
        $query = $this->sort($query);
        
        // data
        $data =  $this->getData($query);
        
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        $model = new StudentUser();
        $profile = new Profile();
        $student = new CommonStudent();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::createItem($model, $profile, $student, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Student successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($id)
    {
        $model = StudentUser::findStudent($id);
        if(!$model || !$model->student || !$model->profile){
            return $this->response(0, _e('Student not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $profile = $model->profile;
        $student = $model->student;
        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::updateItem($model, $profile, $student, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Student successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($id)
    {
        $model = StudentUser::findStudent($id);
        if(!$model || !$model->student || !$model->profile){
            return $this->response(0, _e('Student not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($id)
    {
        $result = StudentUser::deleteItem($id);
        if(!is_array($result)){
            return $this->response(1, _e('Student successfully deleted.'), null, null, ResponseStatus::NO_CONTENT);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }

        
    }

}
