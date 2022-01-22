<?php

namespace api\controllers;

use common\models\model\EduSemestrSubject;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Faculty;
use common\models\model\Student;

class EduSemestrSubjectController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestrSubject';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new EduSemestrSubject();

        $student = Student::findOne(['user_id' => Yii::$app->user->identity->id]);
        if (isset($student)) {
            $eduSemesterIds = EduSemestr::find()
                ->select('id')
                ->where(['edu_plan_id' => $student->edu_plan_id]);

            $query = $model->find()
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['in', 'edu_semestr_id', $eduSemesterIds]);
        } else {
            $query = $model->find()
                ->andWhere(['is_deleted' => 0]);

            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query->andWhere(['faculty_id' => $t['UserAccess']->table_id]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
                ]);
            }
            /*  is Self  */
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new EduSemestrSubject();
        $post = Yii::$app->request->post();

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            // EduSemestr -> EduPlan faculty_id
            $eduSemester = EduSemestr::findOne($post['edu_semestr_id'] ?? null);
            if ($eduSemester) {
                if ($eduSemester->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                }
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $this->load($model, $post);
        $result = EduSemestrSubject::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Edu Semestr Subject successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduSemestrSubject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->eduSemestr->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestrSubject::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Edu Semestr Subject successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduSemestrSubject::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->eduSemestr->eduPlan->faculty_id != $t['UserAccess']->table_id) {
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
        $errors = [];
        $model = EduSemestrSubject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model

        if ($model) {
            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            //            return $t;
            if ($t['status'] == 1) {
                if ($model->eduSemestr->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                    $errors[] = _e('You don\'t have access');
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::FORBIDDEN);
                }
            } elseif ($t['status'] == 2) {
                $errors[] = _e('You don\'t have access or you are not admin');
                return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::FORBIDDEN);
            }
            /*  is Self  */

            $result = EduSemestrSubject::deleteItem($model);

            if (!is_array($result)) {
                return $this->response(1, _e('Edu Semestr Subject succesfully removed.'), null, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
