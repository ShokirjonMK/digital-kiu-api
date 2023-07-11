<?php

namespace api\controllers;

use common\models\model\Countries;
use Yii;
use base\ResponseStatus;

class CountryController extends ApiActiveController
{
    public $modelClass = 'api\resources\Country';

    public function actions()
    {
        return [];
    }

    public $table_name = 'country';
    public $controller_name = 'Country';

    public function actionIndex($lang)
    {
        $model = new Countries();

        $query = $model->find()
            ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $model->tableName().id and tr.table_name = '$model->tableName()'" )
            ->leftJoin("translate tr", "tr.model_id = $model->tableName().id and tr.table_name = '$model->tableName()'")
            ->groupBy($model->tableName() . '.id')
            // ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['tr.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);



        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionView($lang, $id)
    {
        $model = Countries::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
