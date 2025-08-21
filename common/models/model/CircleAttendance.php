<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use yii\behaviors\TimestampBehavior;
use Yii;

class CircleAttendance extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public static function tableName()
    {
        return 'circle_attendance';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['circle_schedule_id', 'student_id', 'date'], 'required'],
            [['circle_id', 'circle_schedule_id', 'student_id', 'teacher_user_id', 'date', 'reason', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['circle_schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => CircleSchedule::className(), 'targetAttribute' => ['circle_schedule_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'circle_id',
            'circle_schedule_id',
            'teacher_user_id',
            'student_id',
            'date',
            'reason',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function getCircle()
    {
        return $this->hasOne(Circle::className(), ['id' => 'circle_id']);
    }

    public function getCircleSchedule()
    {
        return $this->hasOne(CircleSchedule::className(), ['id' => 'circle_schedule_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_user_id']);
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
