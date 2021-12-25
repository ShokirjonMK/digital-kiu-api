<?php

namespace api\controllers;

use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\Exam;
use DateTime;

class ExamController extends ApiActiveController
{
    public $modelClass = 'api\resources\Exam';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam';
    public $controller_name = 'Exam';

    public function actionGeneratePasswords($lang)
    {
        $post = Yii::$app->request->post();
        $result = Exam::generatePasswords($post);

        if (!is_array($result)) {
            return $this->response(1, _e('Passwords successfully generated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        return $result;
    }

    public function actionGetPasswords($lang)
    {
        $post = Yii::$app->request->post();
        $result = Exam::getPasswords($post);
        if (!is_array($result)) {
            return $this->response(1, _e('Passwords for students for this exam'), $result, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        
        return $result;
    }

    public function actionIndex($lang)
    {
        $model = new Exam();

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
        $model = new Exam();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        if (isset($post->start)) {
            $model->start = date('Y-m-d H:i:s', strtotime($post->start));
        }
        if (isset($post->finish)) {
            $model->finish = date('Y-m-d H:i:s', strtotime($post->finish));
        }

        $result = Exam::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Exam::findOne($id);
        $post = Yii::$app->request->post();
        if (isset($post->start)) {
            $model->start = date('Y-m-d H:i:s', strtotime($post->start));
        }
        if (isset($post->finish)) {
            $model->finish = date('Y-m-d H:i:s', strtotime($post->finish));
        }
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $this->load($model, $post);
        $result = Exam::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
