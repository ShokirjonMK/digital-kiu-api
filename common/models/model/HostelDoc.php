<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class HostelDoc extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const IS_CHECKED_TRUE = 1;
    const IS_CHECKED_FALSE = 0;


    const UPLOADS_FOLDER = 'uploads/hostel_doc/';
    public $hostel_file;
    public $hostelFileMaxSize = 1024 * 1024 * 10; // 10 Mb



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hostel_doc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'student_id',
                    'hostel_app_id',
                    'hostel_category_id',
                    // 'hostel_category_type_id',
                ], 'required'
            ],
            [
                [
                    'student_id',
                    'hostel_app_id',
                    'hostel_category_id',
                    'hostel_category_type_id',
                    'type',
                    'is_checked',
                    'status',
                    'order',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted',
                    'archived'
                ], 'integer'
            ],

            [['description', 'conclution'], 'string'],
            [['ball'], 'double'],
            [['file'], 'string', 'max' => 255],

            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['hostel_app_id'], 'exist', 'skipOnError' => true, 'targetClass' => HostelApp::className(), 'targetAttribute' => ['hostel_app_id' => 'id']],
            [['hostel_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => HostelCategory::className(), 'targetAttribute' => ['hostel_category_id' => 'id']],
            [['hostel_category_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => HostelCategoryType::className(), 'targetAttribute' => ['hostel_category_type_id' => 'id']],
            [['start', 'finish'], 'date', 'format' => 'php:Y-m-d'],
            // [['exam_student_id'], 'unique', 'targetAttribute' => ['is_deleted']],

            [['hostel_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->hostelFileMaxSize],

        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'is_checked',
            'student_id',
            'hostel_app_id',
            'hostel_category_id',
            'hostel_category_type_id',
            'type',
            'file',
            'description',
            'user_id',
            'ball',

            'start',
            'finish',
            'conclution',

            'status' => _e('Status'),
            'order' => _e('Order'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }


    public function fields()
    {
        $fields =  [
            'id',

            'is_checked',
            'student_id',
            'hostel_app_id',
            'hostel_category_id',
            'hostel_category_type_id',
            'type',
            'file',
            'description',
            'user_id',
            'ball',

            'order',
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
            'student',
            'hostelApp',
            'hostelCategory',
            'hostelCategoryType',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }



    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    // hostelApp
    public function getHostelApp()
    {
        return $this->hasOne(HostelApp::className(), ['id' => 'hostel_app_id']);
    }

    // hostelCategory
    public function getHostelCategory()
    {
        return $this->hasOne(HostelCategory::className(), ['id' => 'hostel_category_id']);
    }

    // hostelCategoryType
    public function getHostelCategoryType()
    {
        return $this->hasOne(HostelCategoryType::className(), ['id' => 'hostel_category_type_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // hostel file saqlaymiz
        $model->hostel_file = UploadedFile::getInstancesByName('hostel_file');
        if ($model->hostel_file) {
            $model->hostel_file = $model->hostel_file[0];
            $hostelFileUrl = $model->uploadFile();
            if ($hostelFileUrl) {
                $model->file = $hostelFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        $model->user_id = current_user_id();
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $oldFile = $model->file;
        // hostel file saqlaymiz
        $model->hostel_file = UploadedFile::getInstancesByName('hostel_file');
        if ($model->hostel_file) {
            $model->hostel_file = $model->hostel_file[0];
            $hostelFileUrl = $model->uploadFile();
            if ($hostelFileUrl) {
                $model->file = $hostelFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        if ($model->is_checked = HostelDoc::IS_CHECKED_TRUE) {
            if ($model->hostel_category_id > 0) {
                $model->ball = $model->hostelCategoryType ?  $model->hostelCategoryType->ball : null;
            } else {
                $model->ball = $model->hostelCategory ? $model->hostelCategory->ball : null;
            }
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public function uploadFile()
    {

        $folder = self::UPLOADS_FOLDER . $this->student_id . "/";

        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . $folder)) {
                mkdir(STORAGE_PATH  . $folder, 0777, true);
            }

            $fileName = \Yii::$app->security->generateRandomString(8) . '.' . $this->hostel_file->extension;

            $miniUrl = $folder . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->hostel_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH  . $oldFile);
            }
        }
        return true;
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

    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => _e('STATUS_INACTIVE'),
            self::STATUS_ACTIVE => _e('STATUS_ACTIVE'),

        ];
    }
}
