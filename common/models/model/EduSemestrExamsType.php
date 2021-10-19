<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr_exams_type".
 *
 * @property int $id
 * @property int $edu_semestr_subject_id
 * @property int $exams_type_id
 * @property int $max-ball
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrSubject $eduSemestrSubject
 * @property ExamsType $examsType
 */
class EduSemestrExamsType extends \yii\db\ActiveRecord
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
        return 'edu_semestr_exams_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_semestr_subject_id', 'exams_type_id', 'max-ball'], 'required'],
            [['edu_semestr_subject_id', 'exams_type_id', 'max-ball', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['exams_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exams_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_semestr_subject_id' => 'Edu Semestr Subject ID',
            'exams_type_id' => 'Exams Type ID',
            'max-ball' => 'Max Ball',
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
     * Gets query for [[EduSemestrSubject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    /**
     * Gets query for [[ExamsType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamsType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exams_type_id']);
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
