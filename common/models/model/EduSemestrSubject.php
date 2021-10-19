<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr_subject".
 *
 * @property int $id
 * @property int $edu_semestr_id
 * @property int $subject_id
 * @property int $subject_type_id
 * @property float $credit
 * @property int $all_ball_yuklama
 * @property int $is_checked
 * @property int $max_ball
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrExamsType[] $eduSemestrExamsTypes
 * @property EduSemestr $eduSemestr
 * @property Subject $subject
 * @property SubjectType $subjectType
 * @property EduSemestrSubjectCategoryTime[] $eduSemestrSubjectCategoryTimes
 */
class EduSemestrSubject extends \yii\db\ActiveRecord
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
        return 'edu_semestr_subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_semestr_id', 'subject_id', 'subject_type_id', 'credit', 'all_ball_yuklama', 'is_checked', 'max_ball'], 'required'],
            [['edu_semestr_id', 'subject_id', 'subject_type_id', 'all_ball_yuklama', 'is_checked', 'max_ball', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['credit'], 'number'],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::className(), 'targetAttribute' => ['subject_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_semestr_id' => 'Edu Semestr ID',
            'subject_id' => 'Subject ID',
            'subject_type_id' => 'Subject Type ID',
            'credit' => 'Credit',
            'all_ball_yuklama' => 'All Ball Yuklama',
            'is_checked' => 'Is Checked',
            'max_ball' => 'Max Ball',
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
     * Gets query for [[EduSemestrExamsTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrExamsTypes()
    {
        return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id']);
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
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
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::className(), ['id' => 'subject_type_id']);
    }

    /**
     * Gets query for [[EduSemestrSubjectCategoryTimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubjectCategoryTimes()
    {
        return $this->hasMany(EduSemestrSubjectCategoryTime::className(), ['edu_semestr_subject_id' => 'id']);
    }


    public function extraFields()
    {
        $extraFields =  [
//            'department',
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
        if($model->save()){
            $transaction->commit();
            return true;
        }else{
            $errors[] = $model->getErrorSummary(true);
            return simplify_errors($errors);
        }

    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $model->status = 1;
        if($model->save()){
            $transaction->commit();
            return true;
        }else{
            $errors[] = $model->getErrorSummary(true);
            return simplify_errors($errors);
        }
    }


    public function beforeSave($insert) {
        if ($insert) {
            $this->created_by = Yii::$app->user->identity->getId();
        }else{
            $this->updated_by = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }




}
