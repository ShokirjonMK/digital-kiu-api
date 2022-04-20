<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;

use common\models\model\FacultyStatistic;
use common\models\model\Kafedra;
use common\models\model\KafedraStatistic;

class StatisticController extends ApiActiveController
{
    public $modelClass = 'api\resources\statistic';

    public function actions()
    {
        return [];
    }

    public function actionStudentCountByFaculty($lang)
    {
        $model = new FacultyStatistic();

        $table_name = 'faculty';

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

        return 0;
    }

    public function actionQuestionsByKafedra($lang)
    {
        $model = new KafedraStatistic();

        $table_name = 'kafedra';

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

        return 0;
    }
}
