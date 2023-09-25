<?php

namespace api\controllers;

use api\resources\User as ResourcesUser;
use common\models\model\UserStatistic;
use Yii;
use common\models\model\Department;
use common\models\model\Kafedra;
use common\models\model\Profile;
use common\models\model\UserAccess;

use common\models\model\EduPlan;
use common\models\model\EduSemestrSubject;
use common\models\model\ExamStudent;
use common\models\model\ExamStudentAnswer;
use common\models\model\ExamStudentAnswerSubQuestion;
use common\models\model\Faculty;
use common\models\model\FacultyStatistic;
use common\models\model\KafedraStatistic;
use common\models\model\KpiMark;
use common\models\model\StudentTimeTable;
use common\models\model\Subject;
use common\models\model\SubjectContentMark;
use common\models\model\SurveyAnswer;
use common\models\model\SurveyAnswer16;
use common\models\model\TeacherAccess;
use common\models\model\TimeTable;
use common\models\model\UserStatistic1;
use common\models\User;
use yii\db\Expression;
use yii\db\Query;

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
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

        return 0;
    }

    public function actionKafedra($lang)
    {
        return "ok";
        $model = new KafedraStatistic();

        $table_name = 'kafedra';

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);

        return 0;
    }

    public function actionEduPlan($lang)
    {
        return "ok";
        $model = new EduPlan();
        $table_name = 'edu_plan';
        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $table_name.id and tr.table_name = '$table_name'")
            // ->groupBy($table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query->andFilterWhere([
                'faculty_id' => $t['UserAccess']->table_id
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
        }
        // dd('ss');

        /*  is Self  */

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionChecking($lang)
    {
        // return "ok";
        $model = new UserStatistic();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        // $userIds = AuthAssignment::find()->select('user_id')->where([
        //     'in', 'auth_assignment.item_name',
        //     AuthChild::find()->select('child')->where([
        //         'in', 'parent',
        //         AuthAssignment::find()->select("item_name")->where([
        //             'user_id' => current_user_id()
        //         ])
        //     ])
        // ]);

        // $query->andFilterWhere([
        //     'in', 'users.id', $userIds
        // ]);

        /*  is Self  */
        // if(isRole('dean')){

        // }


        if (!(isRole('admin'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
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
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }


    public function actionCheckingChala($lang)
    {
        return "ok";
        $model = new UserStatistic1();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        // $userIds = AuthAssignment::find()->select('user_id')->where([
        //     'in', 'auth_assignment.item_name',
        //     AuthChild::find()->select('child')->where([
        //         'in', 'parent',
        //         AuthAssignment::find()->select("item_name")->where([
        //             'user_id' => current_user_id()
        //         ])
        //     ])
        // ]);

        // $query->andFilterWhere([
        //     'in', 'users.id', $userIds
        // ]);

        /*  is Self  */
        // if(isRole('dean')){

        // }


        if (!(isRole('admin'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
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
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }

    public function actionExamChecking($lang)
    {
        // return "ok";
        $model = new UserStatistic();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->groupBy('users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // dd($query->createCommand()->getRawSql());
        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        if (!(isRole('admin'))) {
            // dd(123);
            $f = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            $d = $this->isSelf(Department::USER_ACCESS_TYPE_ID);

            // faculty
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
        $query = $this->sort($query);


        $users =  $query->all();

        $data = [];
        foreach ($users as $user) {
            $userDATA = [];
            $t = true;
            $teacherAccess =  TeacherAccess::find()->where(['is_deleted' => 0, 'user_id' => $user->id])->all();

            $teacherAccessDATA = [];

            foreach ($teacherAccess as $teacherAccessOne) {
                $examStudent = ExamStudent::find()->where(['is_deleted' => 0, 'teacher_access_id' => $teacherAccessOne->id])->all();
                $examStudentCount = count($examStudent);

                if ($examStudentCount > 0) {

                    $examStudentCheckedCount = 0;

                    foreach ($examStudent as $examStudentOne) {

                        $isChecked = true;
                        $examStudentAnswer = ExamStudentAnswer::find()->where(['is_deleted' => 0, 'exam_student_id' => $examStudentOne->id])->all();
                        $hasAnswer = true;
                        foreach ($examStudentAnswer as $examStudentAnswerOne) {
                            $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::find()
                                ->where(['is_deleted' => 0, 'exam_student_answer_id' => $examStudentAnswerOne->id])
                                ->andWhere(['IS', 'ball', null])
                                ->andWhere(['IS', 'teacher_conclusion', null])
                                ->all();

                            $examStudentAnswerSubQuestionCount = count($examStudentAnswerSubQuestion);

                            if ($examStudentAnswerSubQuestionCount > 0) {
                                $isChecked = false;
                                // foreach ($examStudentAnswerSubQuestion as $examStudentAnswerSubQuestionOne) {
                                //     if (!isNull($examStudentAnswerSubQuestionOne->ball) && !isNull($examStudentAnswerSubQuestionOne->teacher_conclusion)) {
                                //         $isChecked = true;
                                //     }
                                // }
                            }
                        }

                        if ($isChecked) {
                            $examStudentCheckedCount = $examStudentCheckedCount + 1;
                        }
                    }

                    $teacherAccessDATA[]['checkedCount'] = $examStudentCheckedCount;
                    $teacherAccessDATA[]['mustCheckedCount'] = $teacherAccessOne->examStudentCount;
                }
            }

            $userDATA['user'] = $user;
            $userDATA['teacherAccess'] = $teacherAccessDATA;
            $data[] = $userDATA;
        }

        return $data;
    }


    // public function actionKpiContentStore231321321()
    // {
    //     // return "ok";
    //     $model = new UserStatistic();

    //     $query = $model->find()
    //         ->with(['profile'])
    //         ->andWhere(['users.deleted' => 0])
    //         ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
    //         ->groupBy('users.id');

    //     // dd($query->createCommand()->getRawSql());
    //     $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

    //     $errors = [];
    //     $created_by = 7457;
    //     $users = $query->all();
    //     foreach ($users as $userOne) {

    //         $balllllll = SubjectContentMark::find()
    //             ->where([
    //                 'user_id' => $userOne->id,
    //                 'is_deleted' => 0,
    //                 'archived' => 0
    //             ])->andWhere([
    //                 'in', 'subject_id',
    //                 TeacherAccess::find()
    //                     ->where([
    //                         'in', 'subject_id',
    //                         Subject::find()->where(['in', 'semestr_id', [1, 3, 5, 7]])
    //                             ->select('id')
    //                     ])
    //                     ->andWhere([
    //                         'user_id' => $userOne->id,
    //                         'is_deleted' => 0,
    //                         'status' => 1
    //                     ])
    //                     ->select('subject_id')
    //             ])
    //             ->average('ball');



    //         $created = SubjectContentMark::findOne([
    //             'user_id' => $userOne->id,
    //             'is_deleted' => 0,
    //             'archived' => 0
    //         ]);

    //         if ($created) $created_by  = $created->created_by;

    //         $hasKpiMark = KpiMark::findOne([
    //             'user_id' => $userOne->id,
    //             'kpi_category_id' => 8,
    //             'is_deleted' => 0,
    //             'archived' => 0
    //         ]);

    //         if ($hasKpiMark) {
    //             $newKpiMark = $hasKpiMark;
    //         } else {
    //             $newKpiMark = new KpiMark();
    //         }
    //         $newKpiMark->type = 1;
    //         $newKpiMark->created_by = $created_by;
    //         $newKpiMark->kpi_category_id = 8;
    //         $newKpiMark->user_id = $userOne->id;
    //         $newKpiMark->edu_year_id = 17;
    //         $newKpiMark->ball = round($balllllll);
    //         $result = KpiMark::createItemStat($newKpiMark);
    //         if (is_array($result)) {
    //             $errors[] = [$userOne->id => [$newKpiMark, $result]];
    //         }
    //     }

    //     if (count($errors) > 0) {
    //         return $errors;
    //     }
    //     return "ok";
    // }

    public function actionKpiContentStore()
    {
        // Initialize the UserStatistic model
        $model = new UserStatistic();

        // Base query to fetch users who are teachers and not deleted
        $query = $model->find()
            ->with(['profile'])
            ->where(['users.deleted' => 0])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['auth_assignment.item_name' => "teacher"])
            ->groupBy('users.id');

        // Initialize error container
        $errors = [];

        // Default creator ID
        $created_by = 7457;

        // Loop through the user records and process them
        foreach ($query->all() as $userOne) {

            // Sub-query for fetching relevant subject IDs
            $subjectQuery = TeacherAccess::find()
                ->where([
                    'user_id' => $userOne->id,
                    // 'is_deleted' => 0,
                    // 'status' => 1
                ])
                // ->andWhere([
                //     'in', 'subject_id',
                //     Subject::find()->where(['in', 'semestr_id', [1, 3, 5, 7]])->select('id')
                // ])   
                ->select('subject_id');

            // Calculate the average 'ball' value
            $avgBall = SubjectContentMark::find()
                ->where([
                    'user_id' => $userOne->id,
                    'is_deleted' => 0,
                    'archived' => 0
                ])
                ->andWhere(['in', 'subject_id', $subjectQuery])
                ->average('ball');

            // Fetch the creator ID if available
            $created = SubjectContentMark::findOne([
                'user_id' => $userOne->id,
                'is_deleted' => 0,
                'archived' => 0
            ]);
            if ($created) $created_by = $created->created_by;

            // Check for an existing KpiMark entry
            $existingKpiMark = KpiMark::findOne([
                'user_id' => $userOne->id,
                'kpi_category_id' => 8,
                'is_deleted' => 0,
                'archived' => 0
            ]);

            // Initialize a new or existing KpiMark record
            $kpiMark = $existingKpiMark ?? new KpiMark();

            // Update the KpiMark details
            $kpiMark->type = 1;
            $kpiMark->created_by = $created_by;
            $kpiMark->kpi_category_id = 8;
            $kpiMark->user_id = $userOne->id;
            $kpiMark->edu_year_id = 17;
            $kpiMark->ball = round($avgBall);

            // Save or update the KpiMark entry
            $result = KpiMark::createItemStat($kpiMark);
            if (is_array($result)) {
                $errors[] = [$userOne->id => [$kpiMark, $result]];
            }
        }

        // Check if any errors occurred during processing
        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }


    public function actionKpiSurveyStore($i)
    {
        // return "ok";

        /*     SELECT
            time_table.teacher_user_id,
            ROUND( AVG( survey_answer.ball ), 0 ) AS average_ball ,
            AVG( survey_answer.ball )
        FROM
            time_table
            INNER JOIN student_time_table ON time_table.id = student_time_table.time_table_id
            INNER JOIN survey_answer ON student_time_table.student_id = survey_answer.student_id 
            AND time_table.subject_id = survey_answer.subject_id 
        WHERE
            time_table.archived = 1 
        -- 	and time_table.teacher_user_id = 8177
        GROUP BY
            time_table.teacher_user_id
        */

        $model = new UserStatistic();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->groupBy('users.id');

        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        $query = $query->orderBy(['users.id' => SORT_DESC]);
        // $soni = $i * 50;
        // $query = $query->limit(50)->offset($soni);



        $data = [];
        $errors = [];
        $created_by = 7457;

        // dd($query->createCommand()->getRawSql());

        $users = $query->all();
        foreach ($users as $userOne) {

            $surveyAnswerAverage = SurveyAnswer16::find()
                ->where(['in', 'student_id', StudentTimeTable::find()
                    ->where(['in', 'time_table_id', TimeTable::find()
                        ->where([
                            'teacher_user_id' => $userOne->id,
                            'archived' => 1
                        ])
                        ->select('id')])
                    ->select('student_id')])
                ->andWhere([
                    'in',   'subject_id',
                    TimeTable::find()
                        ->where([
                            'teacher_user_id' => $userOne->id,
                            'archived' => 1
                        ])
                        ->select('subject_id')
                ])
                ->average('ball');


            // dd($surveyAnswerAverage->createCommand()->getRawSql());

            $created_by  = 591; // bosit oka

            $hasKpiMark = KpiMark::findOne([
                'user_id' => $userOne->id,
                'kpi_category_id' => 12,
                'is_deleted' => 0,
                'archived' => 0
            ]);

            if ($hasKpiMark) {
                $newKpiMark = $hasKpiMark;
            } else {
                $newKpiMark = new KpiMark();
            }

            $newKpiMark->type = 1;
            $newKpiMark->created_by = $created_by;
            $newKpiMark->kpi_category_id = 12;
            $newKpiMark->user_id = $userOne->id;
            $newKpiMark->edu_year_id = 17;
            $newKpiMark->ball = round($surveyAnswerAverage);
            // $newKpiMark->ball = round($summ / $count);
            // $result = KpiMark::createItemStat($newKpiMark);
            if (!$newKpiMark->save()) {
                $errors[] = [$userOne->id => [$newKpiMark, $newKpiMark->getErrors()]];
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }

    public function actionKpiSurveyStore00($i)
    {
        // return "ok";
        $model = new UserStatistic();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->groupBy('users.id');

        $query = $query->andWhere(['=', 'auth_assignment.item_name', "teacher"]);

        $query = $query->orderBy(['users.id' => SORT_DESC]);
        $soni = $i * 50;
        $query = $query->limit(50)->offset($soni);



        $data = [];
        $errors = [];
        $created_by = 7457;

        // dd($query->createCommand()->getRawSql());

        $users = $query->all();
        foreach ($users as $userOne) {

            $summ = SurveyAnswer::find()
                ->where([
                    'in',  'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in', 'subject_id',
                        TeacherAccess::find()->select('subject_id')
                            ->where([
                                'user_id' => $userOne->id,
                                'is_deleted' => 0
                            ])
                    ])
                ])
                ->sum('ball');

            $count = SurveyAnswer::find()
                ->where([
                    'in',  'edu_semestr_subject_id',
                    EduSemestrSubject::find()->select('id')->where([
                        'in', 'subject_id',
                        TeacherAccess::find()->select('subject_id')
                            ->where([
                                'user_id' => $userOne->id,
                                'is_deleted' => 0
                            ])
                    ])
                ])
                ->count();

            $created_by  = 591; // bosit oka

            if ($count > 0) {

                $hasKpiMark = KpiMark::findOne([
                    'user_id' => $userOne->id,
                    'kpi_category_id' => 12,
                    'is_deleted' => 0,
                    'archived' => 0
                ]);

                if ($hasKpiMark) {
                    $newKpiMark = $hasKpiMark;
                } else {
                    $newKpiMark = new KpiMark();
                }

                $newKpiMark->type = 1;
                $newKpiMark->created_by = $created_by;
                $newKpiMark->kpi_category_id = 12;
                $newKpiMark->user_id = $userOne->id;
                $newKpiMark->edu_year_id = 17;
                $newKpiMark->ball = round($summ / $count);
                $result = KpiMark::createItemStat($newKpiMark);
                if (is_array($result)) {
                    $errors[] = [$userOne->id => [$newKpiMark, $result]];
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return "ok";
    }
}
