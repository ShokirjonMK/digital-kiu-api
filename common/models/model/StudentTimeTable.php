<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\StudentUser;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr".
 *
 * @property int $id
 * @property int $student_id
 * @property int $time_table_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $student
 * @property EduPlan $timeTable
 */
class StudentTimeTable extends \yii\db\ActiveRecord
{

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
        return 'student_time_table';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [['student_id', 'time_table_id'], 'required'],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['time_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['time_table_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student Id',
            'time_table_id' => 'Time Table Id',
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
            'student_id',
            'time_table_id',
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
            'student',
            'timeTable',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTable()
    {
        return $this->hasOne(TimeTable::className(), ['id' => 'time_table_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        /**
         *  Faqat  Student user
         */

        $student = Student::findOne(['user_id' => Yii::$app->user->identity->id]);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            return $errors;
        }
        $model->student_id = $student->id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $hasModel = StudentTimeTable::findOne([
            'student_id' => $model->student_id,
            'time_table_id' => $model->time_table_id,
        ]);

        $studentCheck = Student::findOne($model->student_id);
        $timeTableCheck = TimeTable::findOne($model->time_table_id);


        /**
         *  Student Edu Plan bo'yicah tekshirish
         */

        // if (isset($timeTableCheck)) {
        //     if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
        //         $errors[] = _e('This Time Table is not for this Student');
        //         return $errors;
        //     }
        // }

        if (isset($hasModel)) {
            $errors[] = _e('This Student Time Table already exists ');
            return $errors;
        }

        if ($model->save()) {
            $timeTables = TimeTable::findAll(['parent_id' => $model->time_table_id]);
            if (isset($timeTables)) {
                foreach ($timeTables as $timeTable) {
                    $newModel = new StudentTimeTable();
                    $newModel->student_id = $model->student_id;
                    $newModel->time_table_id = $timeTable->id;
                    $newModel->save();
                }
            }
            $transaction->commit();
            return true;
        } else {
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post, $model_old)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        /**
         *  Faqat  Student user
         */

        $student = Student::findOne(['user_id' => Yii::$app->user->identity->id]);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            return $errors;
        }
        $model->student_id = $student->id;


        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $hasModel = StudentTimeTable::findOne([
            'student_id' => $model->student_id,
            'time_table_id' => $model->time_table_id,
        ]);

        $studentCheck = Student::findOne($model->student_id);
        $timeTableCheck = TimeTable::findOne($model->time_table_id);

        /**
         *    Student Edu Plan bo'chicha tekshirish
         */

        if (isset($timeTableCheck)) {
            if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
                $errors[] = _e('This Time Table is not for this Student');
                return $errors;
            }
        }


        if (isset($hasModel)) {
            $errors[] = _e('This Student Time Table already exists ');
            return $errors;
        }

        $timeTableChilds = TimeTable::find()->select('id')->where(['parent_id' => $model_old->time_table_id]);

        if (isset($timeTableChilds)) {
            StudentTimeTable::deleteAll(['in', 'time_table_id', $timeTableChilds]);
        }

        if ($model->save()) {
            $model_old->delete();
            $timeTables = TimeTable::findAll(['parent_id' => $model->time_table_id]);
            if (isset($timeTables)) {
                foreach ($timeTables as $timeTable) {
                    $newModel = new StudentTimeTable();
                    $newModel->student_id = $model->student_id;
                    $newModel->time_table_id = $timeTable->id;
                    $newModel->save();
                }
            }
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
