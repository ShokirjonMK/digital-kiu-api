<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "{{%attend}}".
 *
 * @property int $id
 * @property string $date
 * @property string|null $student_ids
 * @property int $time_table_id
 * @property int $subject_id
 * @property int $subject_category_id
 * @property int $time_option_id
 * @property int $edu_year_id
 * @property int $edu_semestr_id
 * @property int|null $faculty_id
 * @property int|null $edu_plan_id
 * @property int|null $type 1 kuz 2 bohor
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan $eduPlan
 * @property EduSemestr $eduSemestr
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property StudentAttend[] $studentAttends
 * @property Subject $subject
 * @property SubjectCategory $subjectCategory
 * @property TimeOption $timeOption
 * @property TimeTable $timeTable
 */
class Attend extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attend';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'date',
                'time_table_id',
                // 'subject_id',
                // 'subject_category_id',
                // 'time_option_id',
                // 'edu_year_id',
                // 'edu_semestr_id'
            ], 'required'],
            [[
                'date',
                'student_ids'
            ], 'safe'],
            [[
                'time_table_id',
                'subject_id',
                'subject_category_id', 'time_option_id', 'edu_year_id', 'edu_semestr_id', 'faculty_id', 'edu_plan_id', 'type', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'
            ], 'integer'],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']],
            [['time_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeOption::className(), 'targetAttribute' => ['time_option_id' => 'id']],
            [['time_table_id'], 'exist', 'skipOnError' => true, 'targetClass' => TimeTable::className(), 'targetAttribute' => ['time_table_id' => 'id']],

            [['time_table_id', 'date'], 'unique', 'targetAttribute' => ['time_table_id', 'date'], 'message' => "This TimeTable already exists in this date"]

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'date' => _e('Date'),
            'student_ids' => _e('Student Ids'),
            'time_table_id' => _e('Time Table ID'),
            'subject_id' => _e('Subject ID'),
            'subject_category_id' => _e('Subject Category ID'),
            'time_option_id' => _e('Time Option ID'),
            'edu_year_id' => _e('Edu Year ID'),
            'edu_semestr_id' => _e('Edu Semestr ID'),
            'faculty_id' => _e('Faculty ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'type' => _e('type'),
            'status' => _e('Status'),
            'order' => _e('Order'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',

            'date',
            'student_ids',
            'time_table_id',
            'subject_id',
            'subject_category_id',
            'time_option_id',
            'edu_year_id',
            'edu_semestr_id',
            'faculty_id',
            'edu_plan_id',
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
            'eduPlan',
            'eduSemestr',
            'eduYear',
            'faculty',
            'studentAttends',
            'subject',
            'subjectCategory',
            'timeOption',
            'timeTable',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getAttendance()
    {
        $date = $this->date;

        return 0;
    }


    public function hasAccess()
    {
        $date = $this->date;
        $this->timeTable->eduSemestr;

        /** bir haftalik */
        if ($date >= date('Y-m-d', strtotime('-7 days'))) {
            return 1;
        }
        /** bir haftalik */

        if ($date >= $this->timeTable->eduSemestr->start_date && $date <= $this->timeTable->eduSemestr->end_date) {
            if (isset($date) && $date != null) {
                //     'kun katta ',
                if ($date > date('Y-m-d')) {
                    return 0;
                }
                //     'kun teng ',
                if ($date == date('Y-m-d')) {
                    if (($this->timeTable->week_id == date('w', strtotime($date))) && ($this->timeTable->para->start_time <  date('H:i'))) {
                        return 1;
                    } else {
                        //     'kun teng kirmadi ichiga',
                        return 0;
                    }
                } else {
                    //     'farqi yo faqat week togri ',
                    if (($this->timeTable->week_id == date('w', strtotime($date)))) {
                        return 1;
                    } else {
                        //     'farqi yoo wek hato ',
                        return 0;
                    }
                }
            }
        }
        // 'oxiri 0 ',
        return 0;
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['attend_id' => 'id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery|SubjectCategoryQuery
     */
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    /**
     * Gets query for [[TimeOption]].
     *
     * @return \yii\db\ActiveQuery|TimeOptionQuery
     */
    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

    /**
     * Gets query for [[TimeTable]].
     *
     * @return \yii\db\ActiveQuery|TimeTableQuery
     */
    public function getTimeTable()
    {
        return $this->hasOne(TimeTable::className(), ['id' => 'time_table_id']);
    }

    public static function createItem($model, $post)
    {

        sleep(1);

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }



        $t = false;

        if (isset($post['student_ids'])) {

            if (($post['student_ids'][0] == "'") && ($post['student_ids'][strlen($post['student_ids']) - 1] == "'")) {
                $post['student_ids'] =  substr($post['student_ids'], 1, -1);
            }

            if (!isJsonMK($post['student_ids'])) {
                $errors['student_ids'] = [_e('Must be Json')];
            } else {
                $student_ids = ((array)json_decode($post['student_ids']));
                $model->student_ids = $student_ids;
                $t = true;
            }
        }

        // time_table_id
        $model->subject_id = $model->timeTable->subject_id;
        $model->subject_category_id = $model->timeTable->subject_category_id;
        if ($model->timeTable->time_option_id) {
            $model->time_option_id = $model->timeTable->time_option_id;
        } else {
            $model->time_option_id = $model->timeTable->lecture->time_option_id;
        }
        $model->edu_year_id = $model->timeTable->edu_year_id;
        $model->edu_semestr_id = $model->timeTable->edu_semester_id;
        $model->faculty_id = $model->timeTable->eduPlan->faculty_id;
        $model->edu_plan_id = $model->timeTable->edu_plan_id;
        $model->type = $model->eduSemestr->semestr->type;
        $model->semestr_id = $model->eduSemestr->semestr_id;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // if (!$model->timeTable->getAttendance($model->date)) {
        //     $errors[] = _e("There is no access");
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        if (!$model->hasAccess()) {
            $errors[] = _e("There is no access");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        // if (
        //     !in_array($model->edu_plan_id, [
        //         55, 131
        //     ])
        // ) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        if ($model->save()) {

            if ($t) {
                foreach ($model->student_ids as $student_id) {
                    /** new Student Attent here */

                    /** Checking student is really choos this time table */

                    /** Checking student is really choos this time table */
                    $newStudentAttend = new StudentAttend();
                    $newStudentAttend->student_id = $student_id;
                    $newStudentAttend->attend_id = $model->id;
                    $newStudentAttend->time_table_id = $model->time_table_id;
                    $newStudentAttend->subject_id = $model->subject_id;
                    $newStudentAttend->date = $model->date;
                    $newStudentAttend->subject_category_id = $model->subject_category_id;
                    $newStudentAttend->edu_year_id = $model->edu_year_id;
                    $newStudentAttend->time_option_id = $model->time_option_id;
                    $newStudentAttend->edu_semestr_id = $model->edu_semestr_id;
                    $newStudentAttend->faculty_id = $model->faculty_id;
                    $newStudentAttend->course_id = $model->timeTable->course_id;
                    $newStudentAttend->edu_plan_id = $model->edu_plan_id;
                    $newStudentAttend->type = $model->type;
                    $newStudentAttend->semestr_id = $model->eduSemestr->semestr_id;


                    // $newStudentAttend->reason = $model->reason;
                    if (!$newStudentAttend->save()) {
                        $errors[] = [$student_id => $newStudentAttend->errors];
                    }
                    /** new Student Attent here */
                }
            }

            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }

            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post, $old_student_ids)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $t = false;

        if (isset($post['student_ids'])) {

            if (($post['student_ids'][0] == "'") && ($post['student_ids'][strlen($post['student_ids']) - 1] == "'")) {
                $post['student_ids'] =  substr($post['student_ids'], 1, -1);
            }

            if (!isJsonMK($post['student_ids'])) {
                $errors['student_ids'] = [_e('Must be Json')];
            } else {
                $student_ids = ((array)json_decode($post['student_ids']));
                $model->student_ids = $student_ids;
                $t = true;
            }
        }
        // if (!$model->hasAccess()) {
        //     $errors[] = _e('There is no access to update attend');
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        // if (!in_array($model->edu_plan_id, [
        //     55, 131
        // ])) {
        //     $errors[] = ["Ruxsat berilmagan"];
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        if ($model->save()) {

            $old_deff = array_diff($old_student_ids, $model->student_ids);
            $new_deff = array_diff($model->student_ids, $old_student_ids);


            // if (!isRole('tutor')) {
            if (!empty($old_deff)) {

                if (!StudentAttend::deleteAll([
                    'AND',
                    ['in', 'student_id', $old_deff],
                    ['attend_id' => $model->id]
                ])) {
                    $errors[] = _e('No access to delete student');
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
            }
            // } else {
            //     $model->student_ids = array_merge($old_deff, $model->student_ids);
            // }

            // if (StudentAttend::find()->where(['attend_id' => $model->id])->exists())
            //     if (StudentAttend::deleteAll(['attend_id' => $model->id])) {
            if ($t) {
                foreach ($new_deff as $student_id) {
                    /** new Student Attent here */

                    /** Checking student is really choos this time table */

                    /** Checking student is really choos this time table */

                    // if (!StudentAttend::find()->where(['date' => $model->date, 'student_id' => $student_id, 'attend_id' => $model->id])->exists()) {
                    $newStudentAttend = new StudentAttend();
                    $newStudentAttend->student_id = $student_id;
                    $newStudentAttend->attend_id = $model->id;
                    $newStudentAttend->time_table_id = $model->time_table_id;
                    $newStudentAttend->subject_id = $model->subject_id;
                    $newStudentAttend->date = $model->date;
                    $newStudentAttend->subject_category_id = $model->subject_category_id;
                    $newStudentAttend->edu_year_id = $model->edu_year_id;
                    $newStudentAttend->time_option_id = $model->time_option_id;
                    $newStudentAttend->edu_semestr_id = $model->edu_semestr_id;
                    $newStudentAttend->faculty_id = $model->faculty_id;
                    $newStudentAttend->course_id = $model->timeTable->course_id;
                    $newStudentAttend->edu_plan_id = $model->edu_plan_id;
                    $newStudentAttend->type = $model->type;
                    $newStudentAttend->semestr_id = $model->eduSemestr->semestr_id;

                    // $newStudentAttend->reason = $model->reason;
                    if (!$newStudentAttend->save()) {
                        $errors[] = [$student_id => $newStudentAttend->errors];
                    }
                    // }

                    /** new Student Attent here */
                }
                // }
            } else {
                $errors[] = _e('Error occured in updating');
            }

            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            if ($model->save()) {
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

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
