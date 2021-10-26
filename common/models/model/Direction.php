<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property string $name
 * @property int $faculty_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Faculty $faculty
 * @property EduPlan[] $eduPlans
 * @property Kafedra[] $kafedras
 */
class Direction extends \yii\db\ActiveRecord
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
        return 'direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'faculty_id'], 'required'],
            [['faculty_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'faculty_id' => 'Faculty ID',
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
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyId()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[EduPlans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlans()
    {
        return $this->hasMany(EduPlan::className(), ['direction_id' => 'id']);
    }

    /**
     * Gets query for [[Kafedras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKafedras()
    {
        return $this->hasMany(Kafedra::className(), ['direction_id' => 'id']);
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
