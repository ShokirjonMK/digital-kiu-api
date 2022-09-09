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
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
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
            'subject',
            'teacher',
            'building',
            'room',
            'week',
            'para',
            'subjectCategory',

            'student',
            'timeTable',
        ];

        return $extraFields;
    }


    /* 
    subject,
    teacher,
    building,
    room,
    week,
    para,
    sillabus(seminar | amaliy | ...)
 */



    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategory()
    {
        return $this->timeTable->subjectCategory ?? "";
    }

    /**
     * Gets query for [[para]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPara()
    {
        return $this->timeTable->para ?? "";
    }

    /**
     * Gets query for [[week]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWeek()
    {
        return $this->timeTable->week ?? "";
    }

    /**
     * Gets query for [[room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->timeTable->room ?? "";
    }

    /**
     * Gets query for [[building]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->timeTable->building ?? "";
    }

    /**
     * Gets query for [[teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->timeTable->teacher ?? "";
    }

    /**
     * Gets query for [[subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->timeTable->subject ?? "";
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
        ///

        $student = Student::findOne(['user_id' => Current_user_id()]);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->student_id = $student->id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $hasModel = self::findOne([
            'student_id' => $model->student_id,
            'time_table_id' => $model->time_table_id,
        ]);


        $studentCheck = Student::findOne($model->student_id);
        $timeTableCheck = TimeTable::findOne($model->time_table_id);

        $studentTimeTable = self::find()->where(['time_table_id' => $model->time_table_id, 'is_deleted' => 0])->all();
        if ($model->timeTable->room->capacity < count($studentTimeTable)) {
            $errors[] = _e('This Time Table is Full!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        /**
         *  Student Edu Plan bo'yicah tekshirish
         */

        if (isset($timeTableCheck)) {
            if ($timeTableCheck->eduSemestr) {
                if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
                    $errors[] = _e('This Time Table is not for you');
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
        }

        if (isset($hasModel)) {
            $errors[] = _e('This Student Time Table already exists');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // 

        $timeTableSame = TimeTable::find()->where([
            'edu_semester_id' => $model->timeTable->edu_semester_id,
            'edu_year_id' => $model->timeTable->edu_year_id,
            'subject_id' => $model->timeTable->subject_id,
            'semester_id' => $model->timeTable->semester_id,
            'subject_category_id' => $model->timeTable->subject_category_id,
            'parent_id' => null
        ])->select('id');

        $timeTableSelected = self::find()->where(['in', 'time_table_id', $timeTableSame])->all();

        if (count($timeTableSelected) > 0) {
            $errors[] = _e('This subject already selected');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {

            // Student child larini yozish
            $timeTables = TimeTable::findAll(['parent_id' => $model->time_table_id]);
            if (isset($timeTables)) {
                foreach ($timeTables as $timeTable) {

                    $newModel = new StudentTimeTable();
                    $newModel->student_id = $model->student_id;
                    $newModel->time_table_id = $timeTable->id;
                    if (!$newModel->save()) {
                        $errors[] = _e('Child can not added!');
                    }
                }
            }

            if (count($errors) == 0) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
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

        $student = Student::findOne(['user_id' => Current_user_id()]);
        if (!isset($student)) {
            $errors[] = _e('Student not found');
            $transaction->rollBack();
            return simplify_errors($errors);
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
            if ($timeTableCheck->eduSemestr) {
                if ($timeTableCheck->eduSemestr->edu_plan_id != $studentCheck->edu_plan_id) {
                    $errors[] = _e('This Time Table is not for this Student');
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
        }


        if (isset($hasModel)) {
            $errors[] = _e('This Student Time Table already exists ');
            $transaction->rollBack();
            return simplify_errors($errors);
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

                    if (!$newModel->save()) {
                        $errors[] = _e('Child can not added!');
                    }
                }
            }

            if (count($errors) == 0) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $timeTableChilds = TimeTable::find()->where(['parent_id' => $model->time_table_id])->all();

        if (isset($timeTableChilds)) {

            foreach ($timeTableChilds as $timeTableChildOne) {
                if (!$timeTableChildOne->delete()) {
                    $errors[] = _e('Child ' . $timeTableChildOne->id . ' not deleted!');
                }
            }
        }
        if (count($errors) == 0) {

            if ($model->delete()) {
                $transaction->commit();
                return true;
            } else {
                $errors[] = _e('StudentTimeTable not deleted!');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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
