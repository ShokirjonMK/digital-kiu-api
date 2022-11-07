<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "time_table".
 *
 * @property int $id
 * @property int $teacher_access_id
 * @property int $room_id
 * @property int $para_id
 * @property int $course_id
 * @property int $semester_id
 * @property int $edu_year_id
 * @property int $subject_id
 * @property int $language_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property EduYear $eduYear
 * @property Languages $language
 * @property Para $para
 * @property Room $room
 * @property Subject $subject
 * @property Semestr $semestr
 * @property TeacherAccess $teacherAccess
 */
class TimeTable extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_NEW = 1;
    const STATUS_CHECKED = 2;
    const STATUS_CHANGED = 3;
    const STATUS_INACTIVE = 9;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_table';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'teacher_access_id',
                    'room_id',
                    'para_id',
                    'subject_id',
                    'language_id',
                    'subject_category_id'
                ], 'required'
            ],
            [
                [
                    'teacher_access_id',
                    'room_id',
                    'parent_id',
                    'lecture_id',
                    'para_id',
                    'course_id',
                    'semester_id',
                    'edu_year_id',
                    'subject_id',
                    'language_id',
                    'teacher_user_id',
                    'edu_plan_id',
                    'building_id',
                    'time_option_id',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [
                ['course_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']
            ],
            [
                ['edu_semester_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semester_id' => 'id']
            ],
            [
                ['edu_year_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [
                ['language_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']
            ],
            [
                ['para_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']
            ],
            [
                ['room_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']
            ],
            [
                ['week_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Week::className(), 'targetAttribute' => ['week_id' => 'id']
            ],
            [
                ['subject_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']
            ],
            [
                ['semester_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semester_id' => 'id']
            ],
            [
                ['subject_category_id'], 'exist',
                'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']
            ],
            [
                ['teacher_access_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']
            ],
            [
                ['time_option_id'], 'exist',
                'skipOnError' => true, 'targetClass' => TimeOption::className(), 'targetAttribute' => ['time_option_id' => 'id']
            ],
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
            'room_id' => 'Room ID',
            'para_id' => 'Para ID',
            'time_option_id' => 'time_option_id',
            'course_id' => 'Course ID',
            'edu_plan_id' => 'edu_plan_id',
            'building_id' => 'building_id',
            'lecture_id' => 'Lecture ID',
            'semester_id' => 'Semestr ID',
            'parent_id' => 'Parent ID',
            'subject_category_id ' => 'Subject Category ID',
            'edu_year_id' => 'Edu Year ID',
            'edu_semester_id' => 'Edu Semester ID',
            'subject_id' => 'Subject ID',
            'language_id' => 'Languages ID',
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
        $fields =  [
            'id',
            'teacher_access_id',
            'room_id',
            'para_id',
            'week_id',
            'course_id',
            'semester_id',
            'parent_id',
            'lecture_id',
            'time_option_id',
            'edu_semester_id',
            'edu_year_id',
            'subject_id',
            'language_id',
            'order',
            'edu_plan_id',
            'building_id',
            'subject_category_id',
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

            /** */
            'attendance',
            'now',
            'subjectType',
            'isStudentBusy',
            'subjectCategory',
            'course',
            'attends',
            'studentAttends',
            'eduYear',
            'timeOption',
            'eduPlan',
            'child',
            'parent',
            'seminar',
            'selected',
            'studentTimeTable',
            'studentTimeTables',
            'selectedCount',
            'language',
            'para',
            'room',
            'week',
            'subject',
            'semestr',
            'teacherAccess',
            'eduSemestr',
            'teacher',
            'building',
            'lecture',
            /** */


            'attendanceDates',
            'examControl',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
    /**
     * Gets query for [[ExamControls]]. 
     * 
     * @return \yii\db\ActiveQuery|ExamControlQuery 
     */
    public function getExamControl()
    {
        return $this->hasOne(ExamControl::className(), ['time_table_id' => 'id']);
    } 
 

    public function getAttendanceDates()
    {
        $dateFromString = $this->eduSemestr->start_date;
        $dateToString = $this->eduSemestr->end_date;

        $dateFrom = new \DateTime($dateFromString);
        $dateTo = new \DateTime($dateToString);
        $dates = [];

        if ($dateFrom > $dateTo) {
            return $dates;
        }

        if ($this->week_id != $dateFrom->format('N')) {
            $dateFrom->modify('next ' . $this->dayName()[$this->week_id]);
        }

        while ($dateFrom <= $dateTo) {
            $dates[$dateFrom->format('Y-m-d')] = $this->getAttend($dateFrom->format('Y-m-d'));
            $dateFrom->modify('+1 week');
        }

        return $dates;
    }

    public function dayName()
    {
        return [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ];
    }

    public function getAttendance($date = null)
    {
        $date = $date ?? Yii::$app->request->get('date');


        if (isset($date) && $date != null) {
            if (!($date >= $this->eduSemestr->start_date && $date <= $this->eduSemestr->end_date)) {
                return 0;
            }

            if ($date > date('Y-m-d')) {
                return 0;
            }
            // if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i', strtotime($date))) && ($this->para->end_time >  date('H:i', strtotime($date)))) {
            /* dd([
                $date,
                date('w', strtotime($date)),
                date('H:i', strtotime($date)),
                $this->para->start_time
            ]); */
            // if ($this->eduSemestr->start_date <= $date && $date <= $this->eduSemestr->end_date)

            // if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i', strtotime($date)))) {

            if ($date == date('Y-m-d')) {
                if (($this->week_id == date('w', strtotime($date))) && ($this->para->start_time <  date('H:i'))) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                if (($this->week_id == date('w', strtotime($date)))) {
                    return 1;
                } else {
                    return 0;
                }
            }


            return 0;
        }

        // if (($this->week_id == date('w')) && ($this->para->start_time <  date('H:i')) && ($this->para->end_time >  date('H:i'))) {
        if (($this->week_id == date('w')) && ($this->para->start_time <  date('H:i'))) {
            return 1;
        } else {
            return 0;
        }

        return 0;
    }

    public function getNow()
    {
        return [
            time(),
            date('Y-m-d H:i:s'),
            date('Y-m-d'),
            date('H:i'),
            date('m'),
            date('M'),
            date('w'),
            date('W'),
            date('w', strtotime('2022-10-05')),
        ];

        return [
            $this->para->start_time,
            date('H:i'),
            ($this->para->start_time <  date('H:i')) ? 1 : 0,
            $this->para->end_time,
            ($this->para->end_time >  date('H:i')) ? 1 : 0,

        ];

        if ($this->week_id == date('w')) {
            return 1;
        }

        if ($this->para->start_time <  date('H:i')) {
            return 1;
        }
    }


    public function getSubjectType()
    {
        // return 1;
        $eduSemester = EduSemestrSubject::findOne(
            [
                'subject_id' => $this->subject_id,
                'edu_semestr_id' => $this->edu_semester_id,
            ]
        );

        if ($eduSemester) {
            return $eduSemester->subject_type_id;
        } else {
            return null;
        }
    }

    public function getIsStudentBusy()
    {
        if (isRole('student')) {
            $timeTableSameBusy = TimeTable::find()->where([
                'edu_semester_id' => $this->edu_semester_id,
                'edu_year_id' => $this->edu_year_id,
                'semester_id' => $this->semester_id,
                'para_id' => $this->para_id,
                'week_id' => $this->week_id,
            ])->select('id');

            $timeTableSelected = StudentTimeTable::find()
                ->where(['in', 'time_table_id', $timeTableSameBusy])
                ->andWhere(['student_id' => self::student()])
                ->all();

            if (count($timeTableSelected) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }


    /**
     * Gets query for [
     * [SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }
    // o'quv yili id qo'shish kk
    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [[Attends]].
     *
     * @return \yii\db\ActiveQuery|AttendQuery
     */
    public function getAttends()
    {
        $date = Yii::$app->request->get('date');

        if (isset($date)) {
            $date = date("Y-m-d", strtotime($date));
            return $this->hasMany(Attend::className(), ['time_table_id' => 'id'])->onCondition(['date' => $date])->orderBy('date');
        }

        return $this->hasMany(Attend::className(), ['time_table_id' => 'id'])->orderBy('date');
    }

    public function getAttend($date)
    {
        $date = date("Y-m-d", strtotime($date));
        return Attend::findOne(['time_table_id' => $this->id, 'date' => $date]);
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        if (isRole('student')) {
            return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => $this->student()]);
        }
        return $this->hasMany(StudentAttend::className(), ['time_table_id' => 'id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getTimeOption()
    {
        return $this->hasOne(TimeOption::className(), ['id' => 'time_option_id']);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getChild()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'id']);
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    public function getSeminar()
    {
        return $this->hasMany(self::className(), ['lecture_id' => 'id'])->onCondition(['parent_id' => null]);
    }

    public function getLecture()
    {
        return $this->hasOne(self::className(), ['id' => 'lecture_id'])->onCondition(['parent_id' => null]);
    }

    public function getSelected()
    {
        if (isRole('student')) {

            $studentTimeTable = StudentTimeTable::find()
                ->where([
                    'time_table_id' => $this->id,
                    'student_id' => $this->student()
                ])
                ->all();

            if (count($studentTimeTable) > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        $studentTimeTable = StudentTimeTable::find()->where(['time_table_id' => $this->id])->all();
        return count($studentTimeTable);
    }

    public function getStudentTimeTable()
    {
        return $this->hasOne(StudentTimeTable::className(), ['time_table_id' => 'id'])->onCondition(['student_id' => self::student()]);
    }


    public function getStudentTimeTables()
    {
        return $this->hasMany(StudentTimeTable::className(), ['time_table_id' => 'id']);
    }


    public function getSelectedCount()
    {
        $studentTimeTable = StudentTimeTable::find()->where(['time_table_id' => $this->id])->all();
        return count($studentTimeTable);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id'])->select(['name', 'lang_code']);
    }

    /**
     * Gets query for [[Para]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getWeek()
    {
        return $this->hasOne(Week::className(), ['id' => 'week_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semester_id']);
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

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semester_id']);
    }

    /**
     * Gets query for [[profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return Profile::find()->select(['id', 'first_name', 'last_name', 'middle_name'])->where(['user_id' => $this->teacherAccess->user_id ?? null])->one();
    }

    /**
     * Gets query for [[Building ]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return Building::find()->where(['id' => $this->room->building_id])->one();
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $eduSemester = EduSemestr::findOne($model->edu_semester_id);

        if (isset($post['time_option_id'])) {
            $model->edu_year_id = $model->timeOption->edu_year_id;
            $model->edu_plan_id = $model->timeOption->edu_plan_id;
            $model->edu_year_id = $model->timeOption->edu_year_id;
            $model->edu_semester_id = $model->timeOption->edu_semester_id;
            $model->language_id = $model->timeOption->language_id;
        }

        if (isset($model->parent->time_option_id)) {
            $model->time_option_id = $model->parent->time_option_id;
        }

        if (!isset($eduSemester)) {
            $errors[] = _e("Edu Semester not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $timeTable = TimeTable::findOne([
            'room_id' => $model->room_id,
            'para_id' => $model->para_id,
            'week_id' => $model->week_id,
            'edu_year_id' => $eduSemester->edu_year_id,
        ]);

        $model->semester_id = $eduSemester->semestr_id;
        $model->course_id = $eduSemester->course_id;
        $model->edu_year_id = $eduSemester->edu_year_id;
        $model->edu_plan_id = $eduSemester->edu_plan_id;
        $model->building_id = $model->room->building_id;

        $model->teacher_user_id = $model->teacherAccess->user_id;

        if (isset($timeTable)) {
            if ($model->semester_id % 2 == $timeTable->semester_id % 2) {
                $errors[] = _e("This Room and Para is busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/
        $checkTeacherTimeTable = TimeTable::findOne([
            'para_id' => $model->para_id,
            // 'edu_semester_id' => $model->edu_semester_id,
            'edu_year_id' => $eduSemester->edu_year_id,
            'week_id' => $model->week_id,
            'teacher_access_id' => $model->teacher_access_id,
        ]);

        if (isset($checkTeacherTimeTable)) {
            if ($model->semester_id % 2 == $checkTeacherTimeTable->semester_id % 2) {
                $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/


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
        $eduSemester = EduSemestr::findOne($model->edu_semester_id);

        if (isset($post['time_option_id'])) {
            $childs = TimeTable::updateAll(['time_option_id' => $post['time_option_id']], ['parent_id' => $model->id]);
            // $seminars = TimeTable::updateAll(['time_option_id' => $post['time_option_id']], ['lecture_id' => $model->id]);
        }

        if (!isset($eduSemester)) {
            $errors[] = _e("Edu Semester not found");
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        $timeTable = TimeTable::findOne([
            'room_id' => $model->room_id,
            'para_id' => $model->para_id,
            'week_id' => $model->week_id,
            'edu_year_id' => $eduSemester->edu_year_id,
        ]);

        $model->semester_id = $eduSemester->semestr_id;
        $model->course_id = $eduSemester->course_id;
        $model->edu_year_id = $eduSemester->edu_year_id;

        $model->teacher_user_id = $model->teacherAccess->user_id;

        if (isset($timeTable)) {
            if (($model->semester_id % 2 == $timeTable->semester_id % 2) && ($model->id != $timeTable->id)) {
                $errors[] = _e("This Room and Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }

        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/
        $checkTeacherTimeTable = TimeTable::findOne([
            'para_id' => $model->para_id,
            // 'edu_semester_id' => $model->edu_semester_id,
            'edu_year_id' => $eduSemester->edu_year_id,
            'week_id' => $model->week_id,
            'teacher_access_id' => $model->teacher_access_id,
        ]);

        if (isset($checkTeacherTimeTable)) {
            if (($model->semester_id % 2 == $checkTeacherTimeTable->semester_id % 2) && ($model->id != $checkTeacherTimeTable->id)) {
                $errors[] = _e("This Teacher in this Para are busy for this Edu Year's semestr");
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
        /* Aynan bir kun va bir para boyicha o`qituvchini darsi bo`lsa error qaytadi*/

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
