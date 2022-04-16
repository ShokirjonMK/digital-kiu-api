<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $image
 * @property string $phone
 * @property string $phone_secondary
 * @property int $is_foreign
 * @property string $last_name
 * @property string $first_name
 * @property string $middle_name
 * @property string $passport_seria
 * @property string $passport_number
 * @property string $passport_pin
 * @property int $birthday
 * @property string $passport_file
 * @property int $country_id
 * @property int $region_id
 * @property int $area_id
 * @property string $address
 * @property int $gender
 * @property string $passport_given_date
 * @property string $passport_issued_date
 * @property string $passport_given_by
 * @property int $permanent_country_id
 * @property int $permanent_region_id
 * @property int $permanent_area_id
 * @property string $permanent_address
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int $telegram_chat_id
 *
 * @property Area $area
 * @property Countries $country
 * @property Area $permanentArea
 * @property Countries $permanentCountry
 * @property Region $permanentRegion
 * @property Region $region
 * @property Users $user
 */
class Profile extends \yii\db\ActiveRecord
{

    use ResourceTrait;
    const UPLOADS_FOLDER = 'uploads/user-images/';
    const UPLOADS_FOLDER_STUDENT_IMAGE = 'uploads/student-images/';
    public $avatar;
    // public $passport_file;
    public $avatarMaxSize = 1024 * 200; // 200 Kb
    public $passportFileMaxSize = 1024 * 1024 * 5; // 5 Mb

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
        return 'profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            // [[
            //     'user_id', 'image', 'phone', 'phone_secondary',
            //     'is_foreign', 'last_name', 'first_name', 'middle_name',
            //     'passport_seria', 'passport_number', 'passport_pin',
            //     'birthday', 'passport_file', 'country_id', 'region_id',
            //     'area_id', 'address', 'gender', 'passport_given_date',
            //     'passport_issued_date', 'passport_given_by', 'permanent_country_id',
            //     'permanent_region_id', 'permanent_area_id', 'permanent_address'
            // ], 'required'],


            [
                [
                    'user_id',
                    'nationality_id',
                    'is_foreign',
                    'country_id',
                    'passport_number',
                    'passport_pin',
                    'region_id',
                    'area_id',
                    'gender',
                    'permanent_country_id',
                    'permanent_region_id',
                    'permanent_area_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'telegram_chat_id'
                ],
                'integer'
            ],
            [['passport_given_date', 'birthday',  'passport_issued_date'], 'safe'],
            [
                [
                    'image',
                    'last_name',
                    'first_name',
                    'middle_name',
                    'passport_seria',


                    'passport_file',
                    'address',
                    'passport_given_by',
                    'permanent_address'
                ],
                'string', 'max' => 255
            ],
            [
                [
                    // 'phone',
                    'phone_secondary'
                ], 'string', 'max' => 50
            ],

            [['passport_pin'], 'unique'],

            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],
            [['passport_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, png, jpg', 'maxSize' => $this->passportFileMaxSize],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['permanent_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['permanent_area_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['permanent_country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['permanent_country_id' => 'id']],
            [['permanent_region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['permanent_region_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['nationality_id'], 'exist', 'skipOnError' => true, 'targetClass' => Nationality::className(), 'targetAttribute' => ['nationality_id' => 'id']],
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
            'telegram_chat_id' => 'Telegram Chat ID',
            'nationality_id' => 'Nationality ID',
            'image' => 'Image',
            'phone' => 'Phone',
            'phone_secondary' => 'Phone Secondary',
            'is_foreign' => 'Is Foreign',
            'last_name' => 'Last Name',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'passport_seria' => 'Passport Seria',
            'passport_number' => 'Passport Number',
            'passport_pin' => 'Passport Pin',
            'birthday' => 'Birthday',
            'passport_file' => 'Passport File',
            'country_id' => 'Country ID',
            'region_id' => 'Region ID',
            'area_id' => 'Area ID',
            'address' => 'Address',
            'gender' => 'Gender',
            'passport_given_date' => 'Passport Given Date',
            'passport_issued_date' => 'Passport Issued Date',
            'passport_given_by' => 'Passport Given By',
            'permanent_country_id' => 'Permanent Country ID',
            'permanent_region_id' => 'Permanent Region ID',
            'permanent_area_id' => 'Permanent Area ID',
            'permanent_address' => 'Permanent Address',
            'order' => 'Order',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
        ];
    }

    // public function fields()
    // {
    //     $fields =  [
    //         'id',
    //         'user_id',
    //         'image',
    //         'phone',
    //         'phone_secondary',
    //         'is_foreign',
    //         'last_name',
    //         'first_name',
    //         'middle_name',
    //         'passport_seria',
    //         'passport_number',
    //         'passport_pin',
    //         'birthday',
    //         'passport_file',
    //         'country_id',
    //         'region_id',
    //         'area_id',
    //         'address',
    //         'gender',
    //         'passport_given_date',
    //         'passport_issued_date',
    //         'passport_given_by',
    //         'permanent_country_id',
    //         'permanent_region_id',
    //         'permanent_area_id',
    //         'permanent_address',

    //         'status',
    //         'created_at',
    //         'updated_at',
    //         'created_by',
    //         'updated_by',

    //     ];

    //     return $fields;
    // }



    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    /**
     * Gets query for [[Country]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'country_id']);
    }

    /**
     * Gets query for [[PermanentArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermanentArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'permanent_area_id']);
    }

    /**
     * Gets query for [[PermanentCountry]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermanentCountry()
    {
        return $this->hasOne(Countries::className(), ['id' => 'permanent_country_id']);
    }

    /**
     * Gets query for [[PermanentRegion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermanentRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'permanent_region_id']);
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
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
     * Gets query for [[Nationality]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNationality()
    {
        return $this->hasOne(Nationality::className(), ['id' => 'nationality_id']);
    }

    /**
     * Gets query for [[Citizenship]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCitizenship()
    {
        return $this->hasOne(Citizenship::className(), ['id' => 'citizenship_id']);
    }

    /**
     * Get user fullname
     *
     * @param object $profile
     * @return mixed
     */
    public static function getFullname($profile)
    {
        $fullname = '';

        if ($profile && $profile->first_name) {
            $fullname = _strtotitle($profile->first_name) . ' ';
        }

        if ($profile && $profile->last_name) {
            $fullname .= _strtotitle($profile->last_name);
        }

        return $fullname ? trim($fullname) : 'Unknown User';
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

    public static function deleteItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = Profile::findOne(['id' => $id]);

        if (!isset($model)) {
            $errors[] = [_e('Profile not found')];
        } else {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
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
