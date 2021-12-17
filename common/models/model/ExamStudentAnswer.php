<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "exam_student_answer".
 *
 * @property int $id
 * @property string|null $file
 * @property int $exam_id
 * @property int $question_id
 * @property int $student_id
 * @property int|null $option_id
 * @property string|null $answer
 * @property int|null $ball
 * @property int|null $teacher_access_id
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int $type 1-savol, 2-test, 3-another
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Exam $exam
 * @property ExamQuestion $examQuestion
 * @property ExamQuestionOption $option
 * @property Student $student
 * @property TeacherAccess $teacherAccess
 */
class ExamStudentAnswer extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const STATUS_COMPLETE = 1;
    const STATUS_NEW = 2;
    const STATUS_IN_CHECKING = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_student_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_id', 'question_id', 'student_id', 'type'], 'required'],
            [['exam_id', 'question_id', 'student_id', 'option_id', 'ball', 'teacher_access_id', 'attempt', 'type', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['answer'], 'string'],
            [['file'], 'string', 'max' => 255],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionOption::className(), 'targetAttribute' => ['option_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'File',
            'exam_id' => 'Exam ID',
            'question_id' => ' Question ID',
            'student_id' => 'Student ID',
            'option_id' => 'Option ID',
            'answer' => 'Answer',
            'ball' => 'Ball',
            'teacher_access_id' => 'Teacher Access ID',
            'attempt' => 'Attempt',
            'type' => 'Type',
            'order' => 'Order',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'file',
            'exam_id',
            'question_id',
            'student_id',
            'option_id',
            'answer',
            'ball',
            'teacher_access_id',
            'attempt',
            'type',
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
        $extraFields = [
            'exam',
            'question',
            'option',
            'student',
            'teacherAccess',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
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
     * Gets query for [[ExamQuestion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * Gets query for [[Option]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOption()
    {
        return $this->hasOne(ExamQuestionOption::className(), ['id' => 'option_id']);
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

    public static function randomQuestions($data, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $exam_id = $post["exam_id"];
        $student = Student::findOne(['user_id' => Yii::$app->user->identity->id]);
        // $student_id = $student->id;
        $student_id = 1;
        if (isset($exam_id)) {
            $exam = Exam::findOne($exam_id);
            if (isset($exam)) {
                $hasExamStudentAnswer = ExamStudentAnswer::findOne(['exam_id' => $exam_id, 'student_id' => $student_id]);
                if (isset($hasExamStudentAnswer)) {
                    $data = ExamStudentAnswer::findAll(['exam_id' => $exam_id, 'student_id' => $student_id]);
                    return $data;
                }

                // $now_date = date('Y-m-d H:i:s');
                $now_second = time();
                if (strtotime($exam->start) < $now_second && strtotime($exam->finish) >= $now_second) {
                    $question_count_by_type = json_decode($exam->question_count_by_type);
                    $edu_semestr_subject_id = $exam->eduSemestrSubject->id;
                    $semestr_id = $exam->eduSemestrSubject->eduSemestr->semestr_id;
                    foreach ($question_count_by_type as $type => $question_count) {
                        $questionAll = Question::find()
                            ->where(['subject_id' => $edu_semestr_subject_id, 'semestr_id' => $semestr_id, 'question_type_id' => $type])
                            ->orderBy(new Expression('rand()'))
                            ->limit($question_count)
                            ->all();
                        if (count($questionAll) == $question_count) {
//                        if (count($questionAll) > 0) {
                            foreach ($questionAll as $question) {
                                $ExamStudentAnswer = new ExamStudentAnswer();
                                $ExamStudentAnswer->exam_id = $exam_id;
                                $ExamStudentAnswer->question_id = $question->id;
                                $ExamStudentAnswer->student_id = $student_id;
                                $ExamStudentAnswer->type = $type;
                                $ExamStudentAnswer->status = ExamStudentAnswer::STATUS_NEW;
                                $ExamStudentAnswer->save(false);
                            }
                        } else {
                            ExamStudentAnswer::deleteAll(['exam_id' => $exam_id, 'student_id' => $student_id]);
                            $errors[] = _e("Questions not found for this exam");
                            return $errors;
                        }
                    }
                    $data = ExamStudentAnswer::findAll(['exam_id' => $exam_id, 'student_id' => $student_id]);
                    return $data;
                } else {
                    $errors[] = _e("This exam`s time expired");
                }
            } else {
                $errors[] = _e("This exam not found");
            }
            return simplify_errors($errors);
        }
        return $errors;
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
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {


        // attemp esdan chiqmasin
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            return simplify_errors($errors);
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->getId();
        } else {
            $this->updated_by = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }
}
