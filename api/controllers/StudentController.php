<?php

namespace api\controllers;

use Yii;
use api\resources\StudentUser;
use api\resources\User;

use common\models\Student as CommonStudent;
use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\Profile;
use common\models\model\Student;

class  StudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\Student';

    public function actions()
    {
        return [];
    }
    public function actionIndex($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->groupBy('student.id');


        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']->table_id
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
        }
        /*  is Self  */

        if ($this->isRole('turor')) {
            return 1;
            
            $query = $query->andWhere([
                'tutor_id' => Yii::$app->user->identity->getId()
            ]);
        }

        //  Filter from Profile 
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
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
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $post = Yii::$app->request->post();
        if ($this->isRole('turor')) {
            $post['tutor_id'] = Yii::$app->user->identity->getId();
        }
        $post['role'] = 'student';
        $model = new User();
        $profile = new Profile();
        $student = new Student();

        $users = Student::find()->count();
        $count = $users + 10001;
        $post['username'] = 'tsul-std-' . $count;
        $post['email'] = 'tsul-std' . $count . '@tsul.uz';

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $post['faculty_id'] = $t['UserAccess']->table_id;
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::createItem($model, $profile, $student, $post);
        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully created.'), $data, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($student->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */
        $post['role'] = 'student';

        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model = User::findOne(['id' => $student->user_id]);
        $profile = Profile::findOne(['user_id' => $student->user_id]);


        $this->load($student, $post);
        $result = StudentUser::updateItem($model, $profile, $student, $post);

        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $data, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $result = StudentUser::deleteItem($id);

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully deleted.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        /*
        $model = StudentUser::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $user = User::findOne($model->user_id);
            $user->status = User::STATUS_BANNED;
            $user->save(false);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e('Student succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        */
    }
}
