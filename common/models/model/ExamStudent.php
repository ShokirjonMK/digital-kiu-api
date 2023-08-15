<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "exam_student".
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_id
 * @property int|null $teacher_access_id
 * @property float|null $ball
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Exam $exam
 * @property Student $student
 * @property TeacherAccess $teacherAccess
 * @property Student $exam0
 * @property Student $student0
 * @property TeacherAccess $teacherAccess0
 */
class ExamStudent extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_TAKED = 1;
    const STATUS_COMPLETE = 2;
    const STATUS_IN_CHECKING = 3;
    const STATUS_CHECKED = 4;
    const STATUS_SHARED = 5;

    const IS_PLAGIAT_TRUE = 1;
    const IS_PLAGIAT_FALSE = 0;

    const TYPE_IELTS = 1;
    const TYPE_NOGIRON = 2;
    const TYPE_JAPAN = 3;

    const UPLOADS_FOLDER = 'uploads/exam_student/plagiat_files/';
    const UPLOADS_FOLDER_ACT = 'uploads/exam_student/act_files/';
    public $actFile;
    public $plagiatFile;
    public $fileMaxSize = 1024 * 1024 * 5; // 5 Mb

    // conclusion
    // plagiat_file
    // plagiat_percent

    // act_file

    const ACT_FALSE = 0;
    const ACT_TRUE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'exam_id'], 'required'],
            [
                [
                    'is_checked',
                    'is_checked_full',
                    'has_answer',
                    'student_id',
                    'start',
                    'finish',
                    'exam_id',
                    'subject_id',
                    'edu_semestr_subject_id',
                    'teacher_access_id',
                    'attempt',
                    'lang_id',
                    'exam_semeta_id',
                    'is_plagiat',
                    'duration',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'act',
                    'type',
                    'archived'
                ], 'integer'
            ],
            [['ball', 'in_ball', 'on1', 'on2'], 'double'],

            [['plagiat_file', 'act_reason', 'act_file'], 'string', 'max' => 255],
            [['password'], 'safe'],
            [['plagiat_percent'], 'double'],
            [['conclusion'], 'string'],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['plagiatFile', 'actFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg,jepg,zip,mp4,avi', 'maxSize' => $this->fileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'lang_id' => 'Lang ID',
            'exam_id' => 'Exam ID',
            'teacher_access_id' => 'Teacher Access ID',
            'password' => 'Password',
            'exam_semeta_id' => 'Exam Semeta Id',
            'ball' => 'Ball',
            'duration' => 'Duration',
            'start' => 'Start',
            'type' => 'type',
            'finish' => 'Finish',
            'is_plagiat' => 'Is Plagiat',
            'attempt' => 'Attempt',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'act' => _e('act'),
            'on1' => _e('on1'),
            'on2' => _e('on2'),
            'act_file' => _e('act_file'),
            'act_reason' => _e('act_reason'),


            'in_ball' => _e('in_ball'),
            'is_checked' => _e('is_checked'),
            'is_checked_full' => _e('is_checked_full'),
            'has_answer' => _e('has_answer'),


            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'student_id',
            'exam_id',
            'lang_id',
            'teacher_access_id',
            'ball',
            'attempt',
            'password',
            'is_plagiat',
            'duration',
            // 'finish',
            'finish' => function ($model) {
                return $model->finishedAt;
            },
            // 'start',
            'start' => function ($model) {
                return $model->startedAt;
            },

            'type',
            'on1',
            //  => function ($model) {
            //     return $model->oraliq1;
            // },
            'on2',
            //  => function ($model) {
            //     return $model->oraliq2;
            // },
            'correct',
            'archived',
            'act_file',
            'act_reason',
            'conclusion',
            'plagiat_file',
            'plagiat_percent',
            'reExam',
            // 'examStudentReexam',

            'in_ball',
            'is_checked',
            'is_checked_full',
            'has_answer',

            'act',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'exam',
            'student',

            'examStudentAnswers',

            'answers',
            'hasAnswer',
            'isChecked',
            'isCheckedFull',
            'allBall',
            'oldAllBall',

            'statusName',
            // 'teacherAccess',
            'examSemeta',

            'accessKey',
            'decodedKey',

            'examControlStudent',
            'reExam',
            'examStudentReexam',

            'appeal',
            'examAppeal',
            'teacher',

            'finishedAt',
            'startedAt',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getStartedAt()
    {
        return $this->start ? date('Y-m-d H:i:s', $this->start) : '';
    }


    public function getConclution12()
    {
        if (!isRole('admin')) {
            if (Yii::$app->request->get('subject_id') != null) {
                return ExamConclution::find()
                    ->where(['subject_id' => Yii::$app->request->get('subject_id')])
                    ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
                    ->andWhere(['created_by' => current_user_id()])
                    ->all();
            }
            return ExamConclution::find()
                ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
                ->andWhere(['created_by' => current_user_id()])
                ->all();
        }
        if (Yii::$app->request->get('subject_id') != null) {
            return ExamConclution::find()
                ->where(['subject_id' => Yii::$app->request->get('subject_id')])
                ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
                ->all();
        }
        return ExamConclution::find()
            ->andWhere(['lang_code' => Yii::$app->request->get('lang')])
            ->all();

        return ExamConclution::find()->all();
    }

    public function getConclution()
    {
        $query = ExamConclution::find()
            ->andWhere(['lang_code' => Yii::$app->request->get('lang')]);

        if (!isRole('admin')) {
            $query->andWhere(['created_by' => current_user_id()]);
        }

        if (Yii::$app->request->get('subject_id') != null) {
            $query->andWhere(['subject_id' => Yii::$app->request->get('subject_id')]);
        }

        return $query->all();
    }


    public function getCorrect()
    {
        $on1 = ExamControlStudent::find()
            ->where([
                'student_id' => $this->student_id,
                // 'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
                'subject_id' => $this->exam->eduSemestrSubject->subject_id,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $on1 = $on1->ball ?? null;

        if (is_null($this->on1)) {
            $this->on1 = $on1;
            $this->save();
        }

        $on2 = ExamControlStudent::find()
            ->where([
                'student_id' => $this->student_id,
                // 'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
                'subject_id' => $this->exam->eduSemestrSubject->subject_id,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $on2 = $on2->ball2 ?? null;

        if (is_null($this->on2)) {
            $this->on2 = $on2;
            $this->save();
        }

        return 1;
    }

    public function getOraliq1()
    {
        $on1 = ExamControlStudent::findOne([
            'student_id' => $this->student_id,
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ])->ball ?? null;

        if (is_null($this->on1)) {
            $this->on1 = $on1;
            $this->save();
        }
        return $this->on1;
    }

    public function getOraliq2()
    {
        $on2 = ExamControlStudent::findOne([
            'student_id' => $this->student_id,
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semestr_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ])->ball2 ?? null;

        if (is_null($this->on2)) {
            $this->on2 = $on2;
            $this->save();
        }
        return $this->on2;
    }

    public function getExamControlStudent()
    {
        return ExamControlStudent::findOne([
            'student_id' => $this->student_id,
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semester_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ]);
    }

    public function getExamControl()
    {
        return ExamControl::findOne([
            'edu_semester_id' => $this->exam->eduSemestrSubject->edu_semester_id,
            'subject_id' => $this->exam->eduSemestrSubject->subject_id,
        ]);
    }

    public function getAllBall()
    {
        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->andWhere([
            'in', $model->tableName() . '.exam_student_answer_id',
            ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
        ])
            ->sum('ball');

        return  $query;
    }


    public function getOldAllBall()
    {
        /* $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query
            ->select(['SUM(COALESCE(old_ball, ball))'])
            ->andWhere([
                'in', $model->tableName() . '.exam_student_answer_id',
                ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
            ])
            ->asArray()
            ->one();

        return  $query; */

        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->select(['SUM(COALESCE(old_ball, ball))'])
            ->andWhere([
                'in', $model->tableName() . '.exam_student_answer_id',
                ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
            ]);

        $totalBall = $query->createCommand()->queryScalar();

        return $totalBall;
    }

    public function getFinishedAt()
    {

        // return $this->finish ??
        if ($this->finish > 0) {
            return date("Y-m-d H:i:s", $this->finish);
        } else {
            $exam_finish = $this->start + $this->exam->duration + (int)$this->duration;
            if ($exam_finish > strtotime($this->exam->finish)) {
                return date("Y-m-d H:i:s", strtotime($this->exam->finish));
            } else {
                return date("Y-m-d H:i:s", $exam_finish);
            }
        }

        return "Undefined";
    }

    public function getAccessKey()
    {
        return $this->encodemk5MK($this->id . '-' . $this->student_id);

        return $this->encodeMK($this->student_id) . '-' . $this->encodeMK($this->id);
    }

    public function getDecodedKey()
    {
        return $this->decodemk5MK('ODEwODMtNzg3MQ');

        return $this->encodeMK($this->student_id) . '-' . $this->encodeMK($this->id);
    }

    public function getIsChecked()
    {

        // return $this->examStudentAnswers->examStudentAnswerSubQuestion;

        $model = new ExamStudentAnswer();
        $query = $model->find()->with('examStudentAnswerSubQuestion');

        $query = $query->andWhere([$model->tableName() . '.exam_student_id' => $this->id])
            ->leftJoin("exam_student_answer_sub_question esasq", "esasq.exam_student_answer_id = " . $model->tableName() . " .id ")
            ->andWhere(['esasq.ball' => null, 'esasq.teacher_conclusion' => null])
            ->andWhere([$model->tableName() . '.teacher_conclusion' => null]);

        if (count($query->all()) > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getIsCheckedFull()
    {
        // return $this->id;
        // return $this->examStudentAnswers->examStudentAnswerSubQuestion;

        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->andWhere([
            'in', $model->tableName() . '.exam_student_answer_id',
            ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
        ])
            // ->andWhere([$model->tableName() . '.ball' => null, $model->tableName() . '.teacher_conclusion' => null])
            // // ->orWhere([$model->tableName() . '.teacher_conclusion' => null])
        ;


        $query->andWhere([
            'or',
            [$model->tableName() . '.ball' => null],
            [$model->tableName() . '.teacher_conclusion' => null]
        ]);


        // $model = new ExamStudentAnswer();
        // $query = $model->find();

        // $query = $query->andWhere([$model->tableName() . '.exam_student_id' => $this->id])
        //     ->leftJoin("exam_student_answer_sub_question esasq", "esasq.exam_student_answer_id = " . $model->tableName() . " .id ")
        //     ->andWhere(['esasq.ball' => null, 'esasq.teacher_conclusion' => null])
        //     ->andWhere([$model->tableName() . '.teacher_conclusion' => null]);



        // dd($query->createCommand()->getSql());
        if (count($query->all()) > 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function getHasAnswer()
    {
        $model = new ExamStudentAnswerSubQuestion();
        $query = $model->find();

        $query = $query->andWhere([
            'in', $model->tableName() . '.exam_student_answer_id',
            ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id])
        ]);
        // dd(ExamStudentAnswer::find()->select('id')->where(['exam_student_id' => $this->id]));
        // dd($query->createCommand()->getSql());

        // dd(count($query->all()));jaxcore

        if (count($query->all()) > 0) {
            return 1;
        } else {
            return 0;
        }
    }


    public function getExamStudentAnswers()
    {
        return $this->hasmany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
    }

    public function getAnswers()
    {
        return $this->hasMany(ExamStudentAnswer::className(), ['exam_student_id' => 'id']);
    }

    /**
     * Gets query for [[Exam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['id' => 'exam_id']);
    }

    /**
     * Gets query for [[Exam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamStudentReexam()
    {
        return $this->hasMany(ExamStudentReexam::className(), ['exam_student_id' => 'id']);
    }

    public function getReExam()
    {
        return $this->hasMany(ExamStudentReexam::className(), ['exam_student_id' => 'id']);
    }


    public function getAppeal()
    {
        return $this->hasOne(ExamAppeal::className(), ['exam_student_id' => 'id']);
    }

    public function getExamAppeal()
    {
        return $this->hasOne(ExamAppeal::className(), ['exam_student_id' => 'id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[TeacherAccess]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
        if (current_user_id() == 1) {
        }

        return null;
    }

    public function getTeacher()
    {
        return $this->teacherAccess->profile ?? null;
        if (current_user_id() == 1) {
        }

        return null;
    }

    /**
     * Gets query for [[ExamSemeta]].
     *exam_semeta
     * @return \yii\db\ActiveQuery
     */
    public function getExamSemeta()
    {
        return $this->hasOne(ExamSemeta::className(), ['id' => 'exam_semeta_id']);
    }

    public function getStatusName()
    {
        return   $this->statusList()[$this->status];
    }


    protected static function actionUpdateExamModel($model)
    {
        if ($model->type > 0) {
            $model->ball = $model->allBall;
            $model->is_checked = $model->isChecked;
            $model->is_checked_full = $model->isCheckedFull;
            $model->has_answer = $model->hasAnswer;

            $model->update();
        }

        return $model;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->type = $model->exam->eduSemestrSubject->eduSemestr->type ?? 1;
        $model->edu_year_id = $model->exam->eduSemestrSubject->eduSemestr->edu_year_id;
        // $model->subject_id = $model->exam->eduSemestrSubject->subject_id;

        // $model->exam_id = $examId;
        $model->edu_year_id = $model->exam->eduSemestrSubject->eduSemestr->edu_year_id;
        // $model->student_id = $student_id;
        $model->lang_id = $model->student->edu_lang_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $oldFile = $model->plagiat_file;
        // plagiat file saqlaymiz

        $model->plagiatFile = UploadedFile::getInstancesByName('plagiatFile');
        if ($model->plagiatFile) {
            $model->plagiatFile = $model->plagiatFile[0];
            $plagiatFileUrl = $model->uploadFile();

            if ($plagiatFileUrl) {
                $model->plagiat_file = $plagiatFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***
        // $errors[] = $post['old_file'];


        // $model->status = self::STATUS_CHECKED;
        if ($model->plagiat_percent > Yii::$app->params['plagiat_percent_max']) {
            $model->is_plagiat = self::IS_PLAGIAT_TRUE;
        }
        if ($model->save() && count($errors) == 0) {
            // $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function actItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $oldFile = $model->act_file;
        // act file saqlaymiz

        $model->actFile = UploadedFile::getInstancesByName('actFile');
        if ($model->actFile) {
            $model->actFile = $model->actFile[0];
            $actFileUrl = $model->uploadActFile();

            if ($actFileUrl) {
                $model->act_file = $actFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        $model->act = self::ACT_TRUE;
        $model->act_reason = $post['act_reason'];

        if ($model->save() && count($errors) == 0) {
            // $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteMK($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $examStudentAnswers = ExamStudentAnswer::find()->where(['exam_student_id' => $model->id])->all();

        foreach ($examStudentAnswers as $examStudentAnswerOne) {
            $examStudentAnswerSubQuestion = ExamStudentAnswerSubQuestion::find()->where(['exam_student_answer_id' => $examStudentAnswerOne->id])->all();
            foreach ($examStudentAnswerSubQuestion as $examStudentAnswerSubQuestionOne) {
                $examStudentAnswerSubQuestionDeteledNew = new ExamStudentAnswerSubQuestionDeleted();
                // $examStudentAnswerSubQuestionDeteledNew->load($examStudentAnswerSubQuestionOne, '');

                $examStudentAnswerSubQuestionDeteledNew->exam_student_answer_sub_question_id = $examStudentAnswerSubQuestionOne->id;

                $examStudentAnswerSubQuestionDeteledNew->file = $examStudentAnswerSubQuestionOne->file;
                $examStudentAnswerSubQuestionDeteledNew->exam_student_answer_id = $examStudentAnswerSubQuestionOne->exam_student_answer_id;

                $examStudentAnswerSubQuestionDeteledNew->sub_question_id = $examStudentAnswerSubQuestionOne->sub_question_id;
                $examStudentAnswerSubQuestionDeteledNew->teacher_conclusion = $examStudentAnswerSubQuestionOne->teacher_conclusion;
                $examStudentAnswerSubQuestionDeteledNew->answer = $examStudentAnswerSubQuestionOne->answer;
                $examStudentAnswerSubQuestionDeteledNew->ball = $examStudentAnswerSubQuestionOne->ball;
                $examStudentAnswerSubQuestionDeteledNew->max_ball = $examStudentAnswerSubQuestionOne->max_ball;

                $examStudentAnswerSubQuestionDeteledNew->order = $examStudentAnswerSubQuestionOne->order;
                $examStudentAnswerSubQuestionDeteledNew->status = $examStudentAnswerSubQuestionOne->status;

                $examStudentAnswerSubQuestionDeteledNew->is_deleted = $examStudentAnswerSubQuestionOne->is_deleted;

                $examStudentAnswerSubQuestionDeteledNew->created_at_o = $examStudentAnswerSubQuestionOne->created_at;
                $examStudentAnswerSubQuestionDeteledNew->updated_at_o = $examStudentAnswerSubQuestionOne->updated_at;
                $examStudentAnswerSubQuestionDeteledNew->created_by_o = $examStudentAnswerSubQuestionOne->created_by;
                $examStudentAnswerSubQuestionDeteledNew->updated_by_o = $examStudentAnswerSubQuestionOne->updated_by;

                if (!($examStudentAnswerSubQuestionDeteledNew->save() && $examStudentAnswerSubQuestionOne->delete())) {
                    $errors[] = _e("Deleting on ExamStudentAnswerSubQuestion ID(" . $examStudentAnswerSubQuestionOne->id . ")");
                }
                // return $examStudentAnswerSubQuestionDeteledNew;
            }
            $ExamStudentAnswerDeletedNew = new ExamStudentAnswerDeleted();
            // $ExamStudentAnswerDeletedNew->load($examStudentAnswerOne, '');
            $ExamStudentAnswerDeletedNew->exam_student_answer_id = $examStudentAnswerOne->id;


            $ExamStudentAnswerDeletedNew->exam_id = $examStudentAnswerOne->exam_id;
            $ExamStudentAnswerDeletedNew->question_id = $examStudentAnswerOne->question_id;
            $ExamStudentAnswerDeletedNew->parent_id = $examStudentAnswerOne->parent_id;
            $ExamStudentAnswerDeletedNew->student_id = $examStudentAnswerOne->student_id;
            $ExamStudentAnswerDeletedNew->option_id = $examStudentAnswerOne->option_id;
            $ExamStudentAnswerDeletedNew->teacher_access_id = $examStudentAnswerOne->teacher_access_id;
            $ExamStudentAnswerDeletedNew->exam_student_id = $examStudentAnswerOne->exam_student_id;
            $ExamStudentAnswerDeletedNew->attempt = $examStudentAnswerOne->attempt;
            $ExamStudentAnswerDeletedNew->type = $examStudentAnswerOne->type;

            $ExamStudentAnswerDeletedNew->order = $examStudentAnswerOne->order;
            $ExamStudentAnswerDeletedNew->status = $examStudentAnswerOne->status;
            $ExamStudentAnswerDeletedNew->is_deleted = $examStudentAnswerOne->is_deleted;


            $ExamStudentAnswerDeletedNew->created_at_o = $examStudentAnswerOne->created_at;
            $ExamStudentAnswerDeletedNew->updated_at_o = $examStudentAnswerOne->updated_at;
            $ExamStudentAnswerDeletedNew->created_by_o = $examStudentAnswerOne->created_by;
            $ExamStudentAnswerDeletedNew->updated_by_o = $examStudentAnswerOne->updated_by;

            if (!($ExamStudentAnswerDeletedNew->save() && $examStudentAnswerOne->delete())) {
                $errors[] = _e("Deleting on ExamStudentAnswer ID(" . $examStudentAnswerOne->id . ")");
            }
        }

        $examStudentDeletedNew = new ExamStudentDeleted();
        // $examStudentDeletedNew->load($model, '');
        $examStudentDeletedNew->student_id = $model->student_id;
        $examStudentDeletedNew->exam_student_id = $model->id;
        $examStudentDeletedNew->start = $model->start;
        $examStudentDeletedNew->finish = $model->finish;
        $examStudentDeletedNew->exam_id = $model->exam_id;
        $examStudentDeletedNew->teacher_access_id = $model->teacher_access_id;
        $examStudentDeletedNew->attempt = $model->attempt;
        $examStudentDeletedNew->lang_id = $model->lang_id;
        $examStudentDeletedNew->exam_semeta_id = $model->exam_semeta_id;
        $examStudentDeletedNew->is_plagiat = $model->is_plagiat;
        $examStudentDeletedNew->duration = $model->duration;
        $examStudentDeletedNew->ball = $model->ball;
        $examStudentDeletedNew->plagiat_file = $model->plagiat_file;
        $examStudentDeletedNew->password = $model->password;
        $examStudentDeletedNew->plagiat_percent = $model->plagiat_percent;

        $examStudentDeletedNew->conclusion = $model->conclusion;

        $examStudentDeletedNew->order = $model->order;
        $examStudentDeletedNew->status = $model->status;
        $examStudentDeletedNew->is_deleted = $model->is_deleted;

        $examStudentDeletedNew->created_at_o = $model->created_at;
        $examStudentDeletedNew->updated_at_o = $model->updated_at;
        $examStudentDeletedNew->created_by_o = $model->created_by;
        $examStudentDeletedNew->updated_by_o = $model->updated_by;

        $examStudentDeletedNew->save();

        $model->duration = null;
        $model->start = null;
        $model->status = 0;
        $model->attempt = $model->attempt + 1;

        if ($model->save() && count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public function uploadFile()
    {
        if ($this->validate()) {
            $filePath = self::UPLOADS_FOLDER . $this->exam_id . '/';
            if (!file_exists(STORAGE_PATH . $filePath)) {
                mkdir(STORAGE_PATH . $filePath, 0777, true);
            }

            $fileName = $this->id . "_" . $this->lang_id . "_" . $this->teacher_access_id . "_" . time() . '.' . $this->plagiatFile->extension;

            $miniUrl = $filePath . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->plagiatFile->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }
    public function uploadActFile()
    {
        if ($this->validate()) {
            $filePath = self::UPLOADS_FOLDER_ACT . $this->exam_id . '/';
            if (!file_exists(STORAGE_PATH . $filePath)) {
                mkdir(STORAGE_PATH . $filePath, 0777, true);
            }

            // kim qachonm qilgani yoziladi act fayl nomida
            $fileName = current_user_id() . "_" . time() . "_" . $this->student_id . '.' . $this->actFile->extension;

            $miniUrl = $filePath . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->actFile->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
        return true;
    }

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_TAKED => _e('STATUS_TAKED'),
            self::STATUS_COMPLETE => _e('STATUS_COMPLETE'),
            self::STATUS_IN_CHECKING => _e('STATUS_IN_CHECKING'),
            self::STATUS_CHECKED => _e('STATUS_CHECKED'),
            self::STATUS_SHARED => _e('STATUS_SHARED'),
        ];
    }


    public static function correct($i)
    {
        $soni = $i * 5000;
        // $model = ExamStudent::find()
        //     // ->where(['type' => null])
        //     // ->andWhere(['is_checked_full' => 0])
        //     ->limit(5000)->offset($soni)->all();

        $model = ExamStudent::find()
            // ->where(['type' => null])
            // ->andWhere(['is_checked_full' => 0])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5000)
            ->offset($soni)
            ->all();


        foreach ($model as $modelOne) {
            if (!($modelOne->type > 0)) {

                $modelOne->ball = $modelOne->allBall;

                $modelOne->is_checked = $modelOne->isChecked;
                $modelOne->is_checked_full = $modelOne->isCheckedFull;
                $modelOne->has_answer = $modelOne->hasAnswer;
                $modelOne->update();
            }
        }

        return true;
    }
}
