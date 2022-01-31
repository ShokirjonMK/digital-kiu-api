<?php

namespace api\controllers;

use api\resources\GetTeacher;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\Controller;

class GetTeacherController extends Controller
{

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
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('q')]);

        $query = $query->andWhere(['auth_assignment.item_name' => 'teacher']);

        /** */
        // data
        $perPage = 20;
        $validatePage = true;
        $data =
            new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                    'validatePage' => $validatePage,
                ],
            ]);

        //
        $status = 1;
        $message = _e('Success');
    
        $errors = null;
        $responsStatusCode = 200;

        Yii::$app->response->statusCode = $responsStatusCode;
        $response = [
            'status' => $status,
            'message' => $message
        ];
        if ($data) $response['data'] = $data;
        if ($errors) $response['errors'] = $errors;
        return $response;
    }
}
