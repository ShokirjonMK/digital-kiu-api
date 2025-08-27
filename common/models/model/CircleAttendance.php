<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
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
            [['circle_student_id', 'date'], 'required'],
            [['circle_id', 'circle_student_id', 'circle_schedule_id', 'student_id', 'teacher_user_id', 'reason', 'status', 'is_deleted', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['circle_schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => CircleSchedule::className(), 'targetAttribute' => ['circle_schedule_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['date'], 'safe'],
            // [['start_time', 'end_time'], 'string', 'max' => 10],
            [['teacher_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['teacher_user_id' => 'id']],
            ['reason_text', 'string'],
            ['circle_student_id', 'unique', 'targetAttribute' => ['circle_student_id', 'date', 'is_deleted'], 'message' => 'Attendance already exists'],
        ];
    }

    public function fields()
    {
        return [
            'id',
            'circle_student_id',
            'circle_id',
            'circle_schedule_id',
            'student_id',
            'teacher_user_id',
            'date',
            'reason',
            'status',
            'is_deleted',

            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];
    }

    public function extraFields()
    {
        return [
            'circle',
            'circleSchedule',
            'student',
            'teacher',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
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
        return $this->circleSchedule->teacher;
    }


    /**
     * Bir nechta attendance yozuvlarini yaratish
     */
    public static function createItems($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        try {
            // 1️⃣ Majburiy fieldlar
            if (empty($post['date'])) {
                $errors['date'][] = _e('Date is required');
            }
            if (empty($post['circle_student_ids'])) {
                $errors['circle_student_ids'][] = _e('Circle Student Ids is required');
            }
            if (empty($post['circle_schedule_id'])) {
                $errors['circle_schedule_id'][] = _e('Circle Schedule Id is required');
            }

            if (!empty($errors)) {
                return simplify_errors($errors);
            }

            // 2️⃣ Sana formatlash (normalize string for schedule check, keep int for DB)
            $date = date('Y-m-d', strtotime($post['date']));
            $circle_schedule = CircleSchedule::findOne($post['circle_schedule_id']);
            if (!$circle_schedule) {
                return simplify_errors(['circle_schedule_id' => [_e('CircleSchedule not found')]]);
            }

            // 3️⃣ Sana schedule ichida bo‘lishi kerak
            if (!in_array($date, $circle_schedule->dates)) {
                return simplify_errors(['date' => [_e('Date must be in CircleSchedule->dates')]]);
            }

            // 4️⃣ Student ID lar JSON formatda kelishi kerak
            $circle_student_ids = trim($post['circle_student_ids'], "'");
            if (!isJsonMK($circle_student_ids)) {
                return simplify_errors(['circle_student_ids' => [_e('Must be valid JSON array')]]);
            }
            $circle_student_ids = (array) json_decode($circle_student_ids, true);

            // 5️⃣ Har bir talaba uchun yozuv yaratish
            foreach ($circle_student_ids as $circle_student_id) {
                $circleStudent = CircleStudent::findOne((int)$circle_student_id);
                if (!$circleStudent || $circleStudent->circle_schedule_id != $circle_schedule->id) {
                    $errors[] = ['circle_student_id' => [_e('Incorrect CircleStudent for this CircleSchedule')]];
                    continue;
                }

                $attendance = new CircleAttendance();
                $attendance->circle_student_id  = $circleStudent->id;
                $attendance->circle_schedule_id = $circle_schedule->id;
                $attendance->circle_id          = $circle_schedule->circle_id;
                $attendance->student_id         = $circleStudent->student_id;
                $attendance->teacher_user_id    = $circle_schedule->teacher_user_id;
                // store as integer timestamp per migration
                $attendance->date               = $date;

                if (!$attendance->save()) {
                    $errors[] = $attendance->errors;
                }
            }

            if (empty($errors)) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return simplify_errors([$e->getMessage()]);
        }
    }

    /**
     * Bitta attendance yozuvini yaratish
     */
    public static function createItemOld($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        try {
            // validatsiya
            if (!$model->validate()) {
                return simplify_errors($model->errors);
            }

            $schedule = $model->circleSchedule;
            if (!$schedule) {
                return simplify_errors(['circle_schedule_id' => [_e('CircleSchedule not found')]]);
            }

            $model->circle_schedule_id = $schedule->id;
            $model->circle_id          = $schedule->circle_id;
            $model->teacher_user_id    = $schedule->teacher_user_id;
            // store as integer timestamp per migration
            $model->date               = strtotime($post['date']);

            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                return simplify_errors($model->errors);
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return simplify_errors([$e->getMessage()]);
        }
    }



    public static function createItemsOld($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (empty($post['date'])) {
            $errors['date'] = [_e('Date is required')];
        }

        if (empty($post['circle_student_ids'])) {
            $errors['circle_student_ids'] = [_e('Circle Student Ids is required')];
        }

        if (empty($post['circle_schedule_id'])) {
            $errors['circle_schedule_id'] = [_e('Circle Schedule Id is required')];
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $date = date('Y-m-d', strtotime($post['date'])); // 2025-08-21 date required
        $circle_schedule_id = $post['circle_schedule_id']; // 1 circle_schedule_id required
        $circle_schedule = CircleSchedule::findOne($circle_schedule_id);

        // CircleSchedule has dates
        $dates = $circle_schedule->dates;
        if (!in_array($date, $dates)) {
            $errors['date'] = [_e('Date must be in CircleSchedule->dates')];
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $circle_student_ids = $post['circle_student_ids']; // [1,2,3] circle_student_ids required

        if (isset($circle_student_ids)) {
            if (($circle_student_ids[0] == "'") && ($circle_student_ids[strlen($circle_student_ids) - 1] == "'")) {
                $circle_student_ids =  substr($circle_student_ids, 1, -1);
            }

            if (!isJsonMK($circle_student_ids)) {
                $errors['circle_student_ids'] = [_e('Must be Json')];
            } else {
                $circle_student_ids = ((array)json_decode($circle_student_ids));
            }
        }

        // create items
        foreach ($circle_student_ids as $circle_student_id) {
            $new_model = new CircleAttendance();
            $new_model->circle_student_id = $circle_student_id;
            $new_model->circle_schedule_id = $circle_schedule_id;
            $new_model->circle_id = $circle_schedule->circle_id;
            $new_model->student_id = $circle_schedule->student_id;
            $new_model->teacher_user_id = $circle_schedule->teacher_user_id; // teacher_user_id required

            $new_model->date = $date;

            if (!$new_model->save()) {
                $errors[] = $new_model->errors;
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
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

        $model->circle_schedule_id = $model->circleStudent->circle_schedule_id;
        $model->circle_id = $model->circleStudent->circle_id;
        $model->student_id = $model->circleStudent->student_id;
        $model->teacher_user_id = $model->circleStudent->teacher_user_id;
        $model->date = date('Y-m-d', strtotime($post['date']));

        if (!$model->save()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        return true;
    }


    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post['reason_text'])) {
            $model->reason_text = $post['reason_text'];
            $model->reason = 1;
        }

        if (!$model->save()) {
            $errors[] = $model->errors;
            $transaction->rollBack();
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        return true;
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
