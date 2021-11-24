<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam_teacher_check".
 *
 * @property int $id
 * @property int|null $teacher_access_id
 * @property int $student_id
 * @property int $exam_id
 * @property int|null $attempt Nechinchi marta topshirayotgani
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 */
class ExamTeacherCheck extends \yii\db\ActiveRecord
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
        return 'exam_teacher_check';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_access_id', 'student_id', 'exam_id', 'attempt', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['student_id', 'exam_id'], 'required'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['course_id' => 'id']],
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
            'teacher_access_id' => 'Teacher Access ID',
            'student_id' => 'Student ID',
            'exam_id' => 'Exam ID',
            'attempt' => 'Attempt',
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
            'teacher_access_id',
            'student_id',
            'exam_id',
            'attempt',

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
            'teacherAccess',
            'student',
            'exam',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[TeacherAccess]].
     * teacher_access_id
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['teacher_access_id' => 'id']);
    }

    /**
     * Gets query for [[Student]].
     * student_id
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['student_id' => 'id']);
    }

    /**
     * Gets query for [[Exam]].
     * exam_id
     * @return \yii\db\ActiveQuery
     */
    public function getExam()
    {
        return $this->hasOne(Exam::className(), ['exam_id' => 'id']);
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
