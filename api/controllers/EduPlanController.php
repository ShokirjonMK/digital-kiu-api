<?php

namespace api\controllers;

use common\models\model\EduPlan;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;

class EduPlanController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduPlan';

    public function actions()
    {
        return [];
    }

    public $table_name = 'edu_plan';
    public $controller_name = 'EduPlan';

    public function actionIndex($lang)
    {
        $model = new EduPlan();

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
        $model = new EduPlan();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduPlan::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('EduPlan successfully created.'), $model, null, ResponseStatus::CREATED);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduPlan::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduPlan::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e('EduPlan successfully updated.'), $model, null, ResponseStatus::OK);
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduPlan::find()
            ->andWhere(['id' => $id])
            ->andWhere(['is_deleted' => 0])
            ->one();
            
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = EduPlan::findOne($id);
        if(!$model){
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = EduPlan::findOne($id);

        if($result){
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('EduPlan succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

}