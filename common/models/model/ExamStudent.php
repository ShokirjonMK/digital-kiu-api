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
 * @property Student $exam
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

    const UPLOADS_FOLDER = 'uploads/plagiat_files/';
    public $plagiatFile;
    public $plagiatFileMaxSize = 1024 * 1024 * 5; // 3 Mb

    // conclusion
    // plagiat_file
    // plagiat_percent

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
                    'type'
                ], 'integer'
            ],
            [['ball', 'in_ball', 'on1', 'on2'], 'double'],

            [['plagiat_file'], 'string', 'max' => 255],
            [['password'], 'safe'],
            [['plagiat_percent'], 'double'],
            [['conclusion'], 'string'],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['plagiatFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->plagiatFileMaxSize],

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
            'start',
            'attempt',
            'password',
            'is_plagiat',
            'duration',
            'finish',
            'type',
            'on1',
            'on2',

            'conclusion',
            'plagiat_file',
            'plagiat_percent',

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

            'statusName',
            'teacherAccess',
            'examSemeta',

            'appeal',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
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

        // dd(count($query->all()));

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


    public function getAppeal()
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

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
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

    // public function beforeSave($insert)
    // {
    //     if ($insert) {
    //         $this->created_by = current_user_id();
    //     } else {
    //         $this->updated_by = current_user_id();
    //     }
    //     return parent::beforeSave($insert);
    // }

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
        $soni = $i * 10000;
        $model = ExamStudent::find()->limit(10000)->offset($soni)->all();

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
