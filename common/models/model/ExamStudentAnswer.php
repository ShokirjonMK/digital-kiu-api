<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;

/**
 * This is the model class for table "exam_student_answer".
 *
 * @property int $id
 * @property string|null $file
 * @property int $exam_id
 * @property int $exam_question_id
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
            [['exam_id', 'exam_question_id', 'student_id', 'type'], 'required'],
            [['exam_id', 'exam_question_id', 'student_id', 'option_id', 'ball', 'teacher_access_id', 'attempt', 'type', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['answer'], 'string'],
            [['file'], 'string', 'max' => 255],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['exam_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamQuestion::className(), 'targetAttribute' => ['exam_question_id' => 'id']],
            [['option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamQuestionOption::className(), 'targetAttribute' => ['option_id' => 'id']],
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
            'exam_question_id' => 'Exam Question ID',
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
        $fields =  [
            'id',
            'file',
            'exam_id',
            'exam_question_id',
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
        $extraFields =  [
            'exam',
            'examQuestion',
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
    public function getExamQuestion()
    {
        return $this->hasOne(ExamQuestion::className(), ['id' => 'exam_question_id']);
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
