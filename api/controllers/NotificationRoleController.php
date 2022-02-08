<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\NotificationRole;

class NotificationRoleController extends ApiActiveController
{
    public $modelClass = 'api\resources\Notification';

    public function actions()
    {
        return [];
    }

    public $table_name = 'notification_role';
    public $controller_name = 'NotificationRole';

    /* public function actionIndex($lang)
    {
        $model = new NotificationRole();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            //faqat o'zi yaratgan notiflarni berish
            ->andWhere([$this->table_name . '.created_by' => Current_user_id()])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    } */

    public function actionCreate($lang)
    {
        $model = new NotificationRole();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = NotificationRole::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    /*  public function actionView($lang, $id)
    {
        $model = NotificationRole::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->andWhere(['created_by' => Current_user_id()])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    } */

    public function actionDelete($lang, $id)
    {
        $model = NotificationRole::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->andWhere(['created_by' => Current_user_id()])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
