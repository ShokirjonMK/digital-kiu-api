<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "time_table".
 *
 * @property int $id
 * @property int $teacher_access_id
 * @property int $room_id
 * @property int $para_id
 * @property int $course_id
 * @property int $semestr_id
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
            [['teacher_access_id', 'room_id', 'para_id', 'course_id', 'semestr_id', 'edu_year_id', 'subject_id', 'language_id', 'created_at', 'updated_at'], 'required'],
            [['teacher_access_id', 'room_id', 'para_id', 'course_id', 'semestr_id', 'edu_year_id', 'subject_id', 'language_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['para_id'], 'exist', 'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['teacher_access_id'], 'exist', 'skipOnError' => true, 'targetClass' => TeacherAccess::className(), 'targetAttribute' => ['teacher_access_id' => 'id']],
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
            'course_id' => 'Course ID',
            'semestr_id' => 'Semestr ID',
            'edu_year_id' => 'Edu Year ID',
            'subject_id' => 'Subject ID',
            'language_id' => 'Language ID',
            'order' => 'Order',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }

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
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[Language]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
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

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[TeacherAccessController]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }
}
