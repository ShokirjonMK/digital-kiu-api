<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class ExamControl extends ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'exam_control';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'time_table_id',
                    'course_id',
                    'semester_id',
                    'edu_year_id',
                    'subject_id',
                    'language_id',
                    'faculty_id',
                    'edu_plan_id',
                    'duration',
                    'teacher_user_id',
                    'edu_semester_id',
                    'subject_category_id',
                    'archived',
                    'old_exam_control_id',
                    'direction_id',
                    'type',
                    'category',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['start', 'finish'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['question'], 'string'],
            [['max_ball'], 'double'],
            [['time_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['time_table_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['semester_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semester_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
        ];
    }




    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name in translate
            'time_table_id'=>'time_table_id',
            'course_id'=> 'course_id',
            'semester_id'=> 'semester_id',
            'edu_year_id'=>  'edu_year_id',
            'subject_id'=> 'subject_id',
            'language_id'=> 'language_id',
            'faculty_id'=> 'faculty_id',
            'edu_plan_id'=> 'edu_plan_id',
            'duration'=> 'duration',
            'teacher_user_id'=>'teacher_user_id',
            'edu_semester_id'=> 'edu_semester_id',
            'subject_category_id'=> 'subject_category_id',
            'archived'=>  'archived',
            'old_exam_control_id'=> 'old_exam_control_id',
            'direction_id'=> 'direction_id',
            'type'=> 'type',
            'category'=>  'category',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'status_appeal' => _e('Status appeal'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
         return [
            'id',
            'time_table_id',
            'course_id',
            'semester_id',
            'edu_year_id',
            'subject_id'=>function ($model) {
                return $model->teacherUserId->subject_id ?? '';
            },
            'language_id',
            'faculty_id',
            'edu_plan_id',
            'duration',
            'teacher_user_id',
            'edu_semester_id',
            'subject_category_id'=>function ($model) {
                return $model->teacherUserId->subject_category_id ?? '';
            },
            'archived',
            'old_exam_control_id',
            'direction_id',
            'type',
            'category',
            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    public function getTeacherUserId()
    {
        return $this->hasOne(TimeTable::class, ['id' => 'time_table_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->teacherUserId->teacher_user_id != current_user_id()) {
            $errors[] = _e('This is not your timeTable');
            $transaction->rollBack();
            return simplify_errors($errors);
        }else{
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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
}