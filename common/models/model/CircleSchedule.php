<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Building;
use common\models\model\Room;
use yii\behaviors\TimestampBehavior;
use Yii;

class CircleSchedule extends \yii\db\ActiveRecord
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
        return 'circle_schedule';
    }

    public function rules()
    {
        return [
            // required per comments
            [['circle_id', 'building_id', 'room_id', 'start_date', 'end_date', 'start_time', 'end_time', 'week_id', 'teacher_user_id', 'edu_year_id', 'semestr_type'], 'required'],
            // integers
            [['circle_id', 'building_id', 'room_id', 'week_id', 'abs_count', 'max_student_count', 'student_count', 'teacher_user_id', 'edu_year_id', 'semestr_type', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            // date/time stored as strings or safe
            [['start_date', 'end_date'], 'safe'],
            [['start_time', 'end_time'], 'string', 'max' => 10],
            // existence
            [['circle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Circle::className(), 'targetAttribute' => ['circle_id' => 'id']],
            [['building_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['building_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
            [['week_id'], 'exist', 'skipOnError' => true, 'targetClass' => Week::className(), 'targetAttribute' => ['week_id' => 'id']],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'circle_id' => _e('Circle ID'),
            'building_id' => _e('Building ID'),
            'room_id' => _e('Room ID'),
            'start_date' => _e('Start Date'),  // Y-m-d (migration comment)
            'end_date' => _e('End Date'),  // Y-m-d (migration comment)
            'start_time' => _e('Start Time'),  // e.g., 10:00
            'end_time' => _e('End Time'),  // e.g., 12:00
            'week_id' => _e('Week'),  // hafta id
            'abs_count' => _e('Absent Limit Count'),  // nb lar soni
            'max_student_count' => _e('Max Student Count'),  // maksimal talaba soni
            'student_count' => _e('Student Count'),  // talaba soni
            'teacher_user_id' => _e('Teacher'),
            'edu_year_id' => _e('Edu Year'),
            'semestr_type' => _e('Semestr Type'),  // 1 kuz 2 bahor
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'circle_id',
            'building_id',
            'room_id',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'week_id',
            'abs_count',
            'max_student_count',
            'student_count',
            'teacher_user_id',
            'edu_year_id',
            'semestr_type',
            'status',
            'is_deleted',
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
            'circle',
            'week',
            'building',
            'room',
            'teacher',
            'eduYear',
            'enrollments',
        ];

        return $extraFields;
    }

    public function getCircle()
    {
        return $this->hasOne(Circle::className(), ['id' => 'circle_id']);
    }

    public function getWeek()
    {
        return $this->hasOne(Week::className(), ['id' => 'week_id']);
    }

    public function getBuilding()
    {
        return $this->hasOne(Building::className(), ['id' => 'building_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_user_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEnrollments()
    {
        return $this
            ->hasMany(CircleStudent::className(), ['circle_schedule_id' => 'id'])
            ->where(['is_deleted' => 0]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if (empty($errors)) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if (empty($errors)) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        return simplify_errors($errors);
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
