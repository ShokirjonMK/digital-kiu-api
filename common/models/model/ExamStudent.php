<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

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
            [['student_id', 'start', 'exam_id', 'teacher_access_id', 'attempt', 'lang_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['ball'], 'number'],
            [['password'], 'safe'],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
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
            'ball' => 'Ball',
            'start' => 'Start',
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
            'student_id',
            'exam_id',
            'lang_id',
            'teacher_access_id',
            'ball',
            'start',
            'attempt',
            'password',

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
            'eduSemestrSubject',
            'examType',
            'exam',
            'student',
            'examQuestions',
            'examStudentAnswers',

            'statusName',
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

        if ($model->save()) {
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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_TAKED => _e('STATUS_TAKED'),
            self::STATUS_COMPLETE => _e('STATUS_COMPLETE'),
            self::STATUS_IN_CHECKING => _e('STATUS_IN_CHECKING'),
            self::STATUS_CHECKED => _e('STATUS_MARKED'),
            self::STATUS_SHARED => _e('STATUS_SHARED'),
        ];
    }
}
