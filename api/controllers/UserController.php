<?php

namespace api\controllers;

use api\components\MipServiceMK;
use api\forms\Login;
use Yii;
use api\resources\User;
use base\ResponseStatus;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\model\Department;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Oferta;
use common\models\model\Profile;
use common\models\model\UserAccess;

class UserController extends ApiActiveController
{
    public $modelClass = 'api\resources\User';

    public function actions()
    {
        return [];
    }

    public function actionOferta()
    {
        // return $this->response(1, _e('Oferta comformed.'), current_user_roles_array(), null, ResponseStatus::CREATED);
        $model = new Oferta();
        $post = Yii::$app->request->post();
        $post['role'] = current_user_roles_array();

        $this->load($model, $post);

        $result = Oferta::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Oferta comformed.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
    public function actionGet($pin, $document_issue_date)
    {
        $mip = MipServiceMK::getData($pin, $document_issue_date);

        if ($mip['status']) {
            return $this->response(1, _e('Success'), $mip['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $mip['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionMe()
    {
        $data = null;
        $errors = [];
        $user = User::findOne(Current_user_id());

        if (isset($user)) {
            if ($user->status === User::STATUS_ACTIVE) {
                $profile = $user->profile;

                $isMain = Yii::$app->request->get('is_main') ?? 1;
                if ($isMain == 0) {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        // 'role' => $user->getRoles(),
                        'role' => $user->getRolesStudent(),
                        'oferta' => $user->getOfertaIsComformed(),
                        // 'is_changed' => $user->is_changed,
                        // 'role' => $user->roleItem,
                        'permissions' => $user->permissionsStudent,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } elseif ($isMain == 1) {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        // 'role' => $user->getRoles(),
                        'role' => $user->getRolesNoStudent(),
                        'oferta' => $user->getOfertaIsComformed(),
                        'is_changed' => $user->is_changed,
                        // 'role' => $user->roleItem,
                        'permissions' => $user->permissionsNoStudent,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        // 'role' => $user->getRoles(),
                        'role' => $user->getRoles(),
                        'oferta' => $user->getOfertaIsComformed(),
                        // 'role' => $user->roleItem,
                        'permissions' => $user->permissionsAll,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                }
            } else {
                $errors[] = [_e('User is not active.')];
                return $this->response(1, _e('User is not active'), $data, null, ResponseStatus::UNAUTHORIZED);
            }
            if (count($errors) == 0) {
                return $this->response(1, _e('User successfully refreshed'), $data, null, ResponseStatus::OK);
            } else {
                return ['is_ok' => false, 'errors' => simplify_errors($errors)];
            }
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public function actionLogout()
    {
        if (Login::logout()) {
            return $this->response(1, _e('User successfully Log Out'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('User not found'), null, null, ResponseStatus::NOT_FOUND);
        }
    }

    public function actionIndex($lang)
    {
        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('q')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['!=', 'auth_assignment.item_name', "admin"]);


        $userIds = AuthAssignment::find()->select('user_id')->where([
            'in', 'auth_assignment.item_name',
            AuthChild::find()->select('child')->where([
                'in', 'parent',
                AuthAssignment::find()->select("item_name")->where([
                    'user_id' => current_user_id()
                ])
            ])
        ]);

        $query->andFilterWhere([
            'in', 'users.id', $userIds
        ]);

        /*  is Self  */
        // if(isRole('dean')){
        // }

        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'users.id', UserAccess::find()->select('user_id')->where([
                    'table_id' => $kafedraId,
                    'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                ])
            ]);
        }

        $facultyId = Yii::$app->request->get('faculty_id');
        if (isset($facultyId)) {
            $query->andFilterWhere([
                'in', 'users.id', UserAccess::find()->select('user_id')->where([
                    'table_id' => $facultyId,
                    'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                ])
            ]);
        }

        if (!(isRole('admin')  || isRole('content_assign') || isRole('kpi_check'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
            if (!isRole('mudir')) {
                if ($f['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $f['UserAccess']->table_id,
                            'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                        ])
                    ]);
                }

                // kafedra
                if ($k['status'] == 1) {
                    $query->andFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess']->table_id,
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                        ])
                    ]);
                }
            } else {
                // kafedra
                if ($k['status'] == 1) {
                    $query->orFilterWhere([
                        'in', 'users.id', UserAccess::find()->select('user_id')->where([
                            'table_id' => $k['UserAccess']->table_id,
                            'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                        ])
                    ]);
                }
            }



            // department
            if ($d['status'] == 1) {
                $query->andFilterWhere([
                    'in', 'users.id', UserAccess::find()->select('user_id')->where([
                        'table_id' => $d['UserAccess']->table_id,
                        'user_access_type_id' => Department::USER_ACCESS_TYPE_ID,
                    ])
                ]);
            }
            if ($f['status'] == 2 && $k['status'] == 2 && $d['status'] == 2) {
                $query->andFilterWhere([
                    'users.id' => -1
                ]);
            }
        }
        /*  is Self  */

        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
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

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        // $query = $this->sort($query);

        dd($query->createCommand()->getRawSql());

        // data
        $data = $this->getData($query);
        // $data = $query->all();

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

    public function actionSelfget()
    {
        $model = User::findOne(current_user_id());
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionSelf()
    {
        $model = User::findOne(current_user_id());
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $profile = $model->profile;
        $post = Yii::$app->request->post();

        if (isset($post['username'])) {
            unset($post['username']);
        }
        if (isset($post['access_token'])) {
            unset($post['access_token']);
        }
        if (isset($post['access_token_time'])) {
            unset($post['access_token_time']);
        }
        if (isset($post['password_reset_token'])) {
            unset($post['password_reset_token']);
        }
        if (isset($post['status'])) {
            unset($post['status']);
        }
        if (isset($post['deleted'])) {
            unset($post['deleted']);
        }
        if (isset($post['password_hash'])) {
            unset($post['password_hash']);
        }

        $this->load($model, $post);
        $this->load($profile, $post);
        $result = User::selfUpdateItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Your data successfully updated.'), $model, null, ResponseStatus::OK);
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
