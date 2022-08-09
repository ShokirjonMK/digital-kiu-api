<?php

namespace common\models\model;

use common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "military ".
 *
 * @property int $id
 * @property string $name
 * @property string $lang
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class ParentInfo extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'parent_info';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['last_name', 'user_id','student_id'],'required'],
            [['user_id','student_id','type'], 'integer'],
            [['last_name', 'first_name','middle_name', 'description'], 'string', 'max' => 255],
            [['phone'], 'string', 'max'=> 55],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => _e('User Id'),
            'student_id' => _e('Student Id'),
            'type' => _e('Type'),
            'last_name' => _e('Last Name'),
            'first_name' => _e('First Name'),
            'middle_name' => _e('Middle Name'),
            'description' => _e('Description'),
             'phone' => _e('Phone'),
            'status' => _e('Status'),
            'is_deleted' => _e('Is Deleted'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
        ];
    }

    public function fields()
    {
        $fields = [
            'last_name',
            'first_name',
            'middle_name',
            'description',
            'user_id',
            'student_id',
            'phone',
            'type',
            'status',
            'is_deleted',
            'created_at',
            'updated_at',
            'created_by',
        ];

        return $fields;
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
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

