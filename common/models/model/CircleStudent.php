<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use yii\behaviors\TimestampBehavior;
use Yii;

class CircleStudent extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public static function tableName()
    {
        return 'circle_student';
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
            [['circle_id', 'circle_schedule_id', 'student_user_id', 'student_id'], 'required'],
            [['circle_id', 'circle_schedule_id', 'student_user_id', 'student_id', 'is_finished', 'abs_status', 'certificate_status', 'certificate_date', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['certificate_file'], 'string', 'max' => 255],
            [['circle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Circle::className(), 'targetAttribute' => ['circle_id' => 'id']],
            [['circle_schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => CircleSchedule::className(), 'targetAttribute' => ['circle_schedule_id' => 'id']],
            [['student_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_user_id' => 'id']],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'circle_id',
            'circle_schedule_id',
            'student_user_id',
            'student_id',
            'is_finished',
            'abs_status',
            'certificate_status',
            'certificate_file',
            'certificate_date',
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

    public function getAttendances()
    {
        return $this->hasMany(CircleAttendance::className(), ['circle_student_id' => 'id']);
    }

    public static function createItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $schedule = CircleSchedule::findOne($model->circle_schedule_id);
        if (!$schedule || $schedule->is_deleted) {
            return [_e('Schedule not found.')];
        }

        // per migration: max_student_count limit
        $currentCount = self::find()->where(['circle_schedule_id' => $model->circle_schedule_id, 'is_deleted' => 0])->count();
        if ($currentCount >= (int) $schedule->max_student_count) {
            return [_e('Schedule capacity reached.')];
        }

        // cannot reselect same circle in same semester and year
        $existsSameCircle = self::find()
            ->alias('cs')
            ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
            ->where([
                'cs.student_user_id' => $model->student_user_id,
                'cs.is_deleted' => 0,
                'sch.circle_id' => $schedule->circle_id,
                'sch.edu_year_id' => $schedule->edu_year_id,
                'sch.semestr_type' => $schedule->semestr_type,
            ])
            ->exists();
        if ($existsSameCircle) {
            return [_e('Student already enrolled to this circle in current semester.')];
        }

        // max 2 schedules per semester
        $countThisSemester = self::find()
            ->alias('cs')
            ->innerJoin('circle_schedule sch', 'sch.id = cs.circle_schedule_id')
            ->where([
                'cs.student_user_id' => $model->student_user_id,
                'cs.is_deleted' => 0,
                'sch.edu_year_id' => $schedule->edu_year_id,
                'sch.semestr_type' => $schedule->semestr_type,
            ])
            ->count();
        if ($countThisSemester >= 2) {
            return [_e('Student cannot enroll more than 2 schedules in a semester.')];
        }

        // set derived fields per migration comments
        $model->circle_id = $schedule->circle_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (empty($errors)) {
            if ($model->save()) {
                // keep schedule student_count in sync
                $schedule->student_count = self::find()->where(['circle_schedule_id' => $schedule->id, 'is_deleted' => 0])->count();
                $schedule->save(false);

                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        return simplify_errors($errors);
    }

    
}
