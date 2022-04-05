<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\Languages;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property int $user_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $course_id
 * @property int $edu_year_id
 * @property int $edu_type_id
 * @property int $is_contract
 * @property string $diplom_number
 * @property string $diplom_seria
 * @property string $diplom_date
 * @property string $description
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property Direction $direction
 * @property EduType $eduType
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property Users $user
 */
class Student extends \yii\db\ActiveRecord
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
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    // 'faculty_id',
                    // 'direction_id',
                    // 'course_id',
                    // 'edu_year_id',
                    // 'edu_type_id',
                    // 'is_contract',
                    // 'edu_lang_id'
                ], 'required'
            ],
            [
                [
                    'edu_form_id',
                    'tutor_id',
                    'user_id',
                    'faculty_id',
                    'direction_id',
                    'course_id',
                    'edu_plan_id',
                    'diplom_number',
                    'edu_year_id',
                    'edu_type_id',
                    'is_contract',
                    'order',
                    'status',
                    'gender',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [['diplom_date'], 'safe'],
            [['description'], 'string'],
            [['diplom_seria'], 'string', 'max' => 255],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['tutor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['tutor_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['edu_lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['edu_lang_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'tutor_id' => 'Tutor ID',
            'faculty_id' => 'Faculty ID',
            'direction_id' => 'Direction ID',
            'course_id' => 'Course ID',
            'edu_year_id' => 'Edu Year ID',
            'edu_form_id' => 'Edu Form ID',
            'edu_type_id' => 'Edu Type ID',
            'edu_lang_id' => 'Edu Lang',
            'edu_plan_id' => 'Edu Plan Id',
            'is_contract' => 'Is Contract',
            'diplom_number' => 'Diplom Number',
            'diplom_seria' => 'Diplom Seria',
            'diplom_date' => 'Diplom Date',
            'description' => 'Description',
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
        $fields =  [
            'id',

            'user_id',
            'tutor_id',
            'faculty_id',
            'direction_id',
            'course_id',
            'edu_year_id',
            'edu_form_id',
            'edu_type_id',
            'edu_lang_id',
            'edu_plan_id',
            'is_contract',
            'diplom_number',
            'diplom_seria',
            'diplom_date',
            'description',

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
            'course',
            'direction',
            'eduType',
            'eduYear',
            'eduForm',
            'faculty',
            'user',
            'tutor',
            'profile',
            'eduPlan',
            'citizenship',
            'country',
            'region',
            'area',
            'permanentCountry',
            'permanentRegion',
            'permanentArea',
            'nationality',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    // Profile Citizenship
    public function getCitizenship()
    {
        return Citizenship::findOne($this->profile->citizenship_id) ?? null;
    }

    // getCountry
    public function getCountry()
    {
        return Countries::findOne($this->profile->country_id) ?? null;
    }

    // getRegion
    public function getRegion()
    {
        return Region::findOne($this->profile->region_id) ?? null;
    }

    // getArea
    public function getArea()
    {
        return Area::findOne($this->profile->area_id) ?? null;
    }

    // getPermanentCountry
    public function getPermanentCountry()
    {
        return Countries::findOne($this->profile->permanent_country_id) ?? null;
    }

    // getPermanentRegion
    public function getPermanentRegion()
    {
        return Region::findOne($this->profile->permanent_region_id) ?? null;
    }

    // getPermanentArea
    public function getPermanentArea()
    {
        return Area::findOne($this->profile->permanent_area_id) ?? null;
    }

    // getNationality
    public function getNationality()
    {
        return Nationality::findOne($this->profile->nationality_id) ?? null;
    }


    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
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
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[EduForm]].
     *edu_form_id
     * @return \yii\db\ActiveQuery
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
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
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Tutor]].
     * tutor_id
     * @return \yii\db\ActiveQuery
     */
    public function getTutor()
    {
        return $this->hasOne(User::className(), ['id' => 'tutor_id']);
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
