<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Kafedra;
use common\models\model\Question;
use common\models\model\Subject;
use GuzzleHttp\Psr7\Query;
use Matrix\Decomposition\QR;

class QuestionController extends ApiActiveController
{
    public $modelClass = 'api\resources\Question';

    public function actions()
    {
        return [];
    }

    public $table_name = 'question';
    public $controller_name = 'Question';

    public function actionIndex($lang)
    {
        $model = new Question();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andFilterWhere(['like', 'question', Yii::$app->request->get('q')]);
        if (isRole('mudir')) {

            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
            if ($k['status'] == 1) {

                $query->andFilterWhere([
                    'in', 'subject_id',
                    Subject::find()
                        ->where([' ' => $k['UserAccess']->table_id])
                        ->select('id')
                ]);
            }
    } elseif (isRole("teacher")) {
            $query = $query->andWhere(["created_by" => current_user_id()]);
        }

        // if (isRole('mudir')) {
        //     $seeStatus = [
        //         Question::STATUS_TEACHER_EDITED,
        //         Question::STATUS_INACTIVE,
        //         Question::STATUS_ACTIVE,
        //         Question::STATUS_MUDIR_ACTIVE,
        //         Question::STATUS_MUDIR_REFUSED,
        //         Question::STATUS_DEAN_REFUSED,
        //     ];

        //     $query = $query->andWhere(['in', 'status', $seeStatus]);
        // }
        // if (isRole('dean')) {
        //     $seeStatus = [
        //         Question::STATUS_ACTIVE,
        //         Question::STATUS_MUDIR_ACTIVE,
        //         Question::STATUS_DEAN_ACTIVE,
        //         Question::STATUS_DEAN_REFUSED,
        //         Question::STATUS_EDU_ADMIN_REFUSED,
        //     ];

        //     $query = $query->andWhere(['in', 'status', $seeStatus]);
        // }
        // if (isRole('edu_admin')) {
        //     $seeStatus = [
        //         Question::STATUS_ACTIVE,
        //         Question::STATUS_DEAN_ACTIVE,
        //         Question::STATUS_EDU_ADMIN_REFUSED,
        //     ];

        //     $query = $query->andWhere(['in', 'status', $seeStatus]);
        // }



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
        $model = new Question();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Question::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionStatusUpdate($lang, $id)
    {
        $model = Question::findOne($id);


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        if ($model->status == Question::STATUS_ACTIVE && !isRole('edu_admin')) {
            return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (isRole('edu_admin')) {
            $statusList = [
                Question::STATUS_DEAN_ACTIVE,
                Question::STATUS_EDU_ADMIN_REFUSED,
                Question::STATUS_ACTIVE,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
            $status = $post['status'];
            $post = [];
            $post['status'] = $status;
        }

        if (isRole('dean')) {
            $statusList = [
                Question::STATUS_MUDIR_ACTIVE,
                Question::STATUS_DEAN_ACTIVE,
                Question::STATUS_DEAN_REFUSED,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
            $status = $post['status'];
            $post = [];
            $post['status'] = $status;
        }

        if (isRole('mudir')) {
            $statusList = [
                Question::STATUS_TEACHER_EDITED,
                Question::STATUS_MUDIR_ACTIVE,
                Question::STATUS_MUDIR_REFUSED,
                Question::STATUS_DEAN_REFUSED,
                Question::STATUS_INACTIVE,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }


        if (isRole('teacher') && $model->created_by == current_user_id()) {
            $statusList = [
                Question::STATUS_MUDIR_REFUSED,
                Question::STATUS_DEAN_REFUSED,
                Question::STATUS_EDU_ADMIN_REFUSED,
                Question::STATUS_ACTIVE,
                Question::STATUS_INACTIVE,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        $this->load($model, $post);
        $result = Question::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionUpdate($lang, $id)
    {
        $model = Question::findOne($id);


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        if ($model->status == Question::STATUS_ACTIVE && !isRole('edu_admin')) {
            return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (isRole('edu_admin')) {
            $statusList = [
                Question::STATUS_DEAN_ACTIVE,
                Question::STATUS_EDU_ADMIN_REFUSED,
                Question::STATUS_ACTIVE,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
            $status = $post['status'];
            $post = [];
            $post['status'] = $status;
        }

        if (isRole('dean')) {
            $statusList = [
                Question::STATUS_MUDIR_ACTIVE,
                Question::STATUS_DEAN_ACTIVE,
                Question::STATUS_DEAN_REFUSED,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
            $status = $post['status'];
            $post = [];
            $post['status'] = $status;
        }

        if (isRole('mudir')) {
            $statusList = [
                Question::STATUS_TEACHER_EDITED,
                Question::STATUS_MUDIR_ACTIVE,
                Question::STATUS_MUDIR_REFUSED,
                Question::STATUS_DEAN_REFUSED,
                Question::STATUS_INACTIVE,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }


        if (isRole('teacher') && $model->created_by == current_user_id()) {
            $statusList = [
                Question::STATUS_MUDIR_REFUSED,
                Question::STATUS_DEAN_REFUSED,
                Question::STATUS_EDU_ADMIN_REFUSED,
                Question::STATUS_ACTIVE,
                Question::STATUS_INACTIVE,
            ];
            if (!(in_array($model->status, $statusList, TRUE))) {
                return $this->response(0, _e('Now you can not change!.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }


        $this->load($model, $post);
        $result = Question::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Question::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Question::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
