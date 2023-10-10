<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $user_id
 * @property string $table_name
 * @property int $table_id
 * @property string $role_name
 * 
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property User $User
 */
class UserAccess extends \yii\db\ActiveRecord
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

    // leader status
    const IS_LEADER_TRUE = 1;
    const IS_LEADER_FALSE = 0;


    const WORK_TYPE_MAIN = 1;
    const WORK_TYPE_OUT_MAIN = 2;
    const WORK_TYPE_IN_MAIN = 3;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_access';
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
                    'table_id',
                    'user_access_type_id',
                ], 'required'
            ],
            [
                [
                    'work_rate_id',
                    'job_title_id',
                    'work_type',

                    'user_id',
                    'table_id',
                    'is_leader',
                    'user_access_type_id',

                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ],
                'integer'
            ],
            [['tabel_number'], 'string', 'max' => 22],
            [['role_name', 'table_name'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_access_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccessType::className(), 'targetAttribute' => ['user_access_type_id' => 'id']],
            [['job_title_id'], 'exist', 'skipOnError' => true, 'targetClass' => JobTitle::className(), 'targetAttribute' => ['job_title_id' => 'id']],
            [['work_rate_id'], 'exist', 'skipOnError' => true, 'targetClass' => WorkRate::className(), 'targetAttribute' => ['work_rate_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User Id',
            'is_leader' => 'Is Leader',
            'table_name' => 'Table Name',
            'table_id' => 'Table Id',
            'role_name' => 'Role Name',
            'user_access_type_id' => 'user_access_type_id',
            'work_type' => 'work_type',

            'work_rate_id' => _e('work_rate_id'),
            'job_title_id' => _e('job_title_id'),
            'tabel_number' => _e('tabel_number'),

            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    /*   public function fields()
    {
        $fields =  [
            'id',
            'user_id',
            'is_leader',
            'table_name',
            'table_id',
            'role_name',
            'user_access_type_id',

            'order',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',

        ];
        return $fields;
    } */

    public function extraFields()
    {
        $extraFields =  [
            'user',
            'userAccessType',
            'profile',
            'fullName',
            'workRate',
            'jobTitle',
            'department',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getDepartment()
    {
        return $this->hasOne($this->userAccessType->table_name::className(), ['id' => 'table_id']);
        $data = [];
        $data['user_access_type_id'] = $this->user_access_type_id;
        $data['model'] = $this->userAccessType->table_name::findOne($this->table_id);
        return $data;
    }

    public function getKafedra()
    {
        if ($this->user_access_type_id == 2)
            return $this->hasOne(Kafedra::className(), ['id' => 'table_id']);
        return null;
    }
    public function getFaculty()
    {
        if ($this->user_access_type_id == 2)
            return $this->hasOne(Faculty::className(), ['id' => 'table_id']);
        return null;
    }
    // public function getDepartment()
    // {
    //     if ($this->user_access_type_id == 3)
    //         return $this->hasOne(Department::className(), ['id' => 'table_id']);
    //     return null;
    // }
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getFullName()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id'])->select(['first_name', 'last_name', 'middle_name']);
    }

    public function getWorkRate()
    {
        return $this->hasOne(WorkRate::className(), ['id' => 'work_rate_id']);
    }

    public function getJobTitle()
    {
        return $this->hasOne(JobTitle::className(), ['id' => 'job_title_id']);
    }



    /**
     * Gets query for [[UserAccessType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserAccessType()
    {
        return $this->hasOne(UserAccessType::className(), ['id' => 'user_access_type_id']);
    }

    /**
     * Gets query for [[Access]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccess()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function createItems($user_access_type_id, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $da = [];
        $table_id = isset($post['table_id']) ? $post['table_id'] : null;

        if ($table_id) {
            if (isset($post['user_access'])) {
                $user_access = json_decode(str_replace("'", "", $post['user_access']));
                foreach ($user_access as $user_id) {
                    $user = User::findOne($user_id);
                    $da['user_id'][] = $user_id;

                    $hasUserAccess = UserAccess::findOne([
                        'user_access_type_id' => $user_access_type_id,
                        'table_id' => $table_id,
                        'user_id' => $user_id
                    ]);

                    if ($user) {
                        $da['user'][] = $user->id;
                        if (!($hasUserAccess)) {
                            $da['hasUserAccess'][] = $hasUserAccess;
                            $newUserAccess = new UserAccess();
                            $newUserAccess->user_id = $user_id;
                            $newUserAccess->user_access_type_id = $user_access_type_id;
                            $newUserAccess->table_id = $table_id;
                            if (!($newUserAccess->validate())) {
                                $errors[] = $newUserAccess->errors;
                            } else {
                                $newUserAccess->save();
                            }
                        } else {
                            $errors[] = ['user_id' => [_e('This user already attached (' . $user_id . ')')]];
                        }
                    } else {
                        $errors[] = ['user_id' => [_e('User Id not found (' . $user_id . ')')]];
                    }
                }
            } else {
                $errors[] = ['user_access' => [_e('User Access is required')]];
            }
        } else {
            $errors[] = ['table_id' => [_e('Table Id is required')]];
        }
        // var_dump($da);
        // die();

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
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

    public static function changeLeader($table_id, $user_access_type_id, $user_id)
    {
        $userAccesLast = UserAccess::findOne([
            'table_id' => $table_id,
            'user_access_type_id' => $user_access_type_id,
            'is_leader' => self::IS_LEADER_TRUE
        ]);

        if ($userAccesLast) {
            $userAccesLast->is_leader = self::IS_LEADER_FALSE;
            $userAccesLast->update();
        }


        $userAcces = UserAccess::findOne(['user_id' => $user_id, 'table_id' => $table_id, 'user_access_type_id' => $user_access_type_id]);
        $userAcces->is_leader = self::IS_LEADER_TRUE;
        if ($userAcces->save(false)) {
            return true;
        } else {
            return false;
        }
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
