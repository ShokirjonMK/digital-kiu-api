<?php

namespace common\models\model;

use common\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "olympic_certificate".
 *
 * @property int $id
 * @property string $address
 * @property string $year
 * @property string $file
 * @property int $student_id
 * @property int $other_certificate_type_id
 * @property int $user_id
 * @property int $status
 * @property int $is_deleted
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 */
class OtherCertificate extends \yii\db\ActiveRecord
{

    public $img;
    const UPLOADS_FOLDER = 'fileOliympic';
    public $imgMaxSize = 1024 * 1024 * 10; // 3 Mb

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
        return 'other_certificate';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['address','other_certificate_type_id', 'user_id','student_id'],'required'],
            [['user_id','student_id'], 'integer'],
            [['address'], 'string', 'max' => 255],
            [['year'], 'string', 'max'=> 4],
            [['file'], 'string', 'max' => 255],
            [['img'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,png,jpg', 'maxSize' => $this->imgMaxSize],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['other_certificate_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\model\OtherCertificateType::class, 'targetAttribute' => ['other_certificate_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'file'=>_e('File'),
            'address'=>_e('Address'),
            'other_certificate_type_id'=>_e('Other Certificate Type Id'),
            'year'=>_e('Year'),
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
            'file',
            'user_id',
            'other_certificate_type_id',
            'address',
            'year',
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
            $model->img = UploadedFile::getInstancesByName('img');

            if ($model->img) {
                $model->img = $model->img[0];
                $imgFile = $model->uploadFile();
                if ($imgFile) {
                    $model->file = $imgFile;
                } else {
                    $errors[] = $model->errors;
                }
            }

            if ($model->save()) {
                $model->deleteFile();
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        }
    }


    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        $oldFile = $model->file;
        $model->img = UploadedFile::getInstancesByName('img');
        if ($model->img) {
            $model->img = $model->img[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->deleteFile($oldFile);
                $model->file = $questionFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($model->save()) {
            $model->deleteFile($oldFile);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

    }

    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(UPLOADS_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(UPLOADS_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }
            $fileName = $this->id . '_' . \Yii::$app->security->generateRandomString() . '.' . $this->img->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = UPLOADS_PATH . $miniUrl;
            $this->img->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists( $oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }


    #region rel
    public function getUsers()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    public function getStudents()
    {
        return $this->hasOne(\common\models\model\Student::class, ['id' => 'student_id']);
    }

    public function getOtherTypIds()
    {
        return $this->hasOne(\common\models\model\OtherCertificateType::class, ['id' => 'other_certificate_type_id']);
    }

    #endregion


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

