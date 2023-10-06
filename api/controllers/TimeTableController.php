<?php

namespace api\controllers;

use common\models\model\TimeTable;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\StudentTimeTable;
use common\models\model\Subject;

class TimeTableController extends ApiActiveController
{
    public $modelClass = 'api\resources\TimeTable';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new TimeTable();

        $student = Student::findOne(['user_id' => current_user_id()]);
        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            // ->andWhere(['archived' => 0])
        ;

        if (isRole('student')) {
            if ($student) {
                $query->andWhere(['in', 'edu_semester_id', EduSemestr::find()->where(['edu_plan_id' => $student->edu_plan_id])->select('id')]);
                $query->andWhere(['language_id' => $student->edu_lang_id]);
            }
        } else {

            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {

                $query->andFilterWhere([
                    'in', 'subject_id', Subject::find()->where([
                        'kafedra_id' => $k['UserAccess']->table_id
                    ])->select('id')
                ]);
            }
        }

        if (isRole('teacher') && !isRole('mudir') && !isRole('dean')) {
            $query->andFilterWhere([
                'teacher_user_id' => current_user_id()
            ]);
        }

        $mudirSelf = Yii::$app->request->get('self');

        if (isset($mudirSelf))
            if ($mudirSelf == 1)  $query->andFilterWhere([
                'teacher_user_id' => current_user_id(),
                'archived' => 0,
            ]);

        $kafedraId = Yii::$app->request->get('kafedra_id');

        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'subject_id', Subject::find()->where([
                    'kafedra_id' => $kafedraId
                ])->select('id')
            ]);
        }

        $facultyId = Yii::$app->request->get('faculty_id');
        if (isset($facultyId)) {
            $query->andFilterWhere([
                'in', 'subject_id', Subject::find()->where([
                    'kafedra_id' => Kafedra::find()->where(['faculty_id' => $facultyId])->select('id')
                ])->select('id')
            ]);
        }

        /** filter with array subject_category_ids */
        $statuses = json_decode(str_replace("'", "", Yii::$app->request->get('subject_category_ids')));

        if ($statuses) {
            $query->andFilterWhere([
                'in', 'subject_category_id',
                $statuses
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        // dd($query->createCommand()->getRawSql());

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionParentNull($lang)
    {
        $model = new TimeTable();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            // ->andWhere(['archived' => 0])
            ->andWhere(['parent_id' => null])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);


        $student = Student::findOne(['user_id' => current_user_id()]);

        if ($student && isRole('student')) {

            // /** Kurs bo'yicha vaqt belgilash */
            // $errors = [];
            // if (!StudentTimeTable::chekTime()) {
            //     $errors[] = _e('This is not your time to choose!');
            //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
            // }
            // /** Kurs bo'yicha vaqt belgilash */

            $query->andWhere(['in', 'edu_semester_id', EduSemestr::find()->where(['edu_plan_id' => $student->edu_plan_id])->select('id')]);
            $query->andWhere(['language_id' => $student->edu_lang_id]);
        } else {

            // $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            // if ($k['status'] == 1) {

            //     $query->andFilterWhere([
            //         'in', 'subject_id', Subject::find()->where([
            //             'kafedra_id' => $k['UserAccess']->table_id
            //         ])->select('id')
            //     ]);
            // }
        }

        // if (isRole('teacher') && !isRole('mudir')) {
        //     $query->andFilterWhere([
        //         'teacher_user_id' => current_user_id()
        //     ]);
        // }

        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'subject_id', Subject::find()->where([
                    'kafedra_id' => $kafedraId
                ])->select('id')
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        /* $errors = [];
        if (StudentTimeTable::TIME_10 < time()) {
            $errors[] = _e('Students started choosing!');
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        } */
        $model = new TimeTable();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TimeTable::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        /* $errors = [];
        if (StudentTimeTable::TIME_10 < time()) {
            $errors[] = _e('Students started choosing!');
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        } */

        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = TimeTable::updateItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TimeTable::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = TimeTable::findOne($id);

        if ($result) {
            TimeTable::deleteAll(['parent_id' => $result->id]);
            $result->delete();

            return $this->response(1, _e('TimeTable and its children succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
