<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "edu_semestr".
 *
 * @property int $id
 * @property int $edu_plan_id
 * @property int $course_id
 * @property int $semestr_id
 * @property int $edu_year_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $is_checked
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property EduPlan $eduPlan
 * @property EduYear $eduYear
 * @property Semestr $semestr
 * @property EduSemestrSubject[] $eduSemestrSubjects
 */
class EduSemestr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_semestr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_plan_id', 'course_id', 'semestr_id', 'edu_year_id', 'created_at', 'updated_at'], 'required'],
            [['edu_plan_id', 'course_id', 'semestr_id', 'edu_year_id', 'is_checked', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_plan_id' => 'Edu Plan ID',
            'course_id' => 'Course ID',
            'semestr_id' => 'Semestr ID',
            'edu_year_id' => 'Edu Year ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_checked' => 'Is Checked',
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
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
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
     * Gets query for [[Semestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[EduSemestrSubjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubjects()
    {
        return $this->hasMany(EduSemestrSubject::className(), ['edu_semestr_id' => 'id']);
    }
}
