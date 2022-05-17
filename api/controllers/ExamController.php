<?php

namespace api\controllers;

use common\models\model\EduSemestr;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestrSubject;
use common\models\model\Exam;
use common\models\model\ExamSemeta;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use common\models\model\TeacherAccess;

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
        if (is_array($result)) {
            if ($result['is_ok']) {
                // unset($result['is_ok']);
                return $this->response(1, _e('Passwords for students for this exam'), $result, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $result;
    }

    public function actionIndex($lang)
    {
        $model = new Exam();
        $student = Student::findOne(['user_id' => Current_user_id()]);
        // return $student;
        $eduSmesterId = Yii::$app->request->get('edu_semestr_id');

        $query = $model->find()->with(['infoRelation']);

        $query = $query->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            // ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('q')]);

        $statuses = json_decode(str_replace("'", "", Yii::$app->request->get('statuses')));

        if ($statuses) {
            $query->andFilterWhere([
                'in', $this->table_name . '.status',
                $statuses
            ]);
        }


        $subjectId = Yii::$app->request->get('subject_id');
        if ($subjectId) {
            $query = $query->andFilterWhere(['in', 'edu_semestr_subject_id', EduSemestrSubject::find()
                ->where(['subject_id' => $subjectId])
                ->andWhere(['is_deleted' => 0])
                ->select('id')]);
        }

        if ($student) {
            //            dd($student->edu_plan_id);
            if (isset($eduSmesterId)) {
                $query = $query->andWhere([
                    'in', 'edu_semestr_subject_id', EduSemestrSubject::find()
                        ->andWhere(['edu_semestr_id' => $eduSmesterId])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            } else {

                $query = $query->andWhere([
                    'in', 'edu_semestr_subject_id', EduSemestrSubject::find()
                        ->where(['in', 'edu_semestr_id', EduSemestr::find()
                            ->where(['edu_plan_id' => $student->edu_plan_id])
                            ->andWhere(['is_deleted' => 0])
                            ->select('id')])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            }
            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $query = $query->andFilterWhere([
                'in', 'edu_semestr_subject_id',
                EduSemestrSubject::find()
                    ->where(['subject_id' => $this->teacher_access(1, ['subject_id'])])
                    ->andWhere(['is_deleted' => 0])
                    ->select('id')
            ]);

            $query = $query->andWhere([
                $this->table_name . '.status' => Exam::STATUS_DISTRIBUTED
            ]);

            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (isRole('mudir')) {
            /*  is Self  */
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
            if ($k['status'] == 1) {
                $query = $query->andFilterWhere([
                    'in', 'edu_semestr_subject_id',
                    EduSemestrSubject::find()->where([
                        'in', 'subject_id',
                        Subject::find()->where(['kafedra_id' => $k['UserAccess']->table_id, 'is_deleted' => 0])->select('id')
                    ])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            } elseif ($k['status'] == 2) {
                $query->andFilterWhere([
                    $this->table_name . '.faculty_id' => -1
                ]);
            }
            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
            /*  is Self  */
        } else {
            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    $this->table_name . '.faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    $this->table_name . '.faculty_id' => -1
                ]);
            }
            /*  is Self  */

            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }
    }

    public function actionCreate($lang)
    {
        $model = new Exam();
        $post = Yii::$app->request->post();
        // $post['duration'] =  strtotime($post['duration']);

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

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

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->facuty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

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

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
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

        // return $model->eduSemestrSubject->subject->kafedra_id;

        if (isRole('mudir')) {
            /*  is Self  */
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
            if ($k['status'] == 1) {
                if (!($model->eduSemestrSubject->subject->kafedra_id == $k['UserAccess']->table_id)) {
                    return $this->response(0, _e('You do not have acceess.'), null, null, ResponseStatus::FORBIDDEN);
                }
            }
            /*  is Self  */
        }
        // else {
        // /*  is Self  */
        // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     if ($model->facuty_id != $t['UserAccess']->table_id) {
        //         return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        //     }
        // } elseif ($t['status'] == 2) {
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        // }
        // /*  is Self  */
        // }


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

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->facuty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionDistribution($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = ExamSemeta::distribution($model);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' succesfully distributed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAd($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->status == Exam::STATUS_ANNOUNCED) {
            $model->status = Exam::STATUS_DISTRIBUTED;
        } else {
            $model->status = Exam::STATUS_ANNOUNCED;
        }
        if ($model->save()) {
            return $this->response(1, _e($this->controller_name . ' succesfully announced.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
