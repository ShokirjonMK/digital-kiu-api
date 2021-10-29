<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_plan".
 *
 * @property int $id
 * @property int $course
 * @property int $semestr
 * @property int $edu_year_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $edu_type_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction $direction
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property EduType $eduType
 * @property EduSemestr[] $eduSemestrs
 */
class EduPlan extends \yii\db\ActiveRecord
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
        return 'edu_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['course', 'semestr', 'edu_year_id', 'faculty_id', 'direction_id', 'edu_type_id'], 'required'],
            [['course', 'semestr', 'edu_year_id', 'faculty_id', 'direction_id', 'edu_type_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course' => 'Course',
            'semestr' => 'Semestr',
            'edu_year_id' => 'Edu Year ID',
            'faculty_id' => 'Faculty ID',
            'direction_id' => 'Direction ID',
            'edu_type_id' => 'Edu Type ID',
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
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
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
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[EduSemestrs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrs()
    {
        return $this->hasMany(EduSemestr::className(), ['edu_plan_id' => 'id']);
    }

    public function extraFields()
    {
        $extraFields =  [
            'direction',
            'eduYear',
            'faculty',
            'eduType',
            'eduSemestrs',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->status = 1;
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->getErrorSummary(true);
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->status = 1;
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $errors[] = $model->getErrorSummary(true);
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
