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

            ->andFilterWhere(['like', 'name', Yii::$app->request->get('q')]);

        //filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


}
