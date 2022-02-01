<?php

namespace api\controllers;

use api\resources\GetTeacher;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\rest\Controller;

class GetTeacherController extends ActiveController
{

    use ApiOpen;

    public $modelClass = 'api\resources\Country';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new GetTeacher();
        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            // ->select(['profile.first_name'])
        ;

        $query = $query->andWhere(['auth_assignment.item_name' => 'teacher']);

        $query->andFilterWhere(['like', 'last_name', Yii::$app->request->get('q')]);
        // $query->orFilterWhere(['like', 'first_name', Yii::$app->request->get('q')]);
        // $query->orFilterWhere(['like', 'middle_name', Yii::$app->request->get('q')]);
        /** */
        // data

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }
}
