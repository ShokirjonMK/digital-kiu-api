<?php

namespace api\controllers;

use Yii;
use api\resources\User;
use base\ResponseStatus;
use common\models\model\Profile;

class UserController extends ApiActiveController
{
    public $modelClass = 'api\resources\User';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new User();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->join('INNER JOIN', 'auth_assignment as aa', 'aa.user_id = users.id')
            // ->andFilterWhere(['like', 'username', Yii::$app->request->get('q')])
            ;

        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if($attribute == 'role_name' ){
                     $query = $query->andFilterWhere(['auth_assignment.' . $attribute => $id]);
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        //filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        $model = new User();
        $profile = new Profile();
        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $this->load($profile, $post);
        $result = User::createItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $profile = $model->profile;
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $this->load($profile, $post);
        $result = User::updateItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($id)
    {
        $model = User::find()
            ->with(['profile'])
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->andWhere(['users.id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($id)
    {
        $result = User::deleteItem($id);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully deleted.'), null, null, ResponseStatus::NO_CONTENT);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionStatusList()
    {
        return $this->response(1, _e('Success.'), User::statusList(), null, ResponseStatus::OK);
    }
}
