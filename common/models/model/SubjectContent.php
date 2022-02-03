<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 *
 * @property int $name
 * @property int $time
 * @property string $subject_id
 * @property string $lang_id
 * @property int $description
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan[] $eduPlans
 */
class SubjectContent extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const TYPE_TEXT = 1;
    const TYPE_FILE = 2;
    const TYPE_IMAGE = 3;
    const TYPE_VIDEO = 4;
    const TYPE_AUDIO = 5;

    public $file_file;
    public $file_image;
    public $file_video;
    public $file_audio;

    const UPLOADS_FOLDER = 'uploads/content_files';
    public $file_fileFileMaxSize = 1024 * 1024 * 5; // 3 Mb
    public $file_imageFileMaxSize = 1024 * 1024 * 2; // 3 Mb
    public $file_videoFileMaxSize = 1024 * 1024 * 25; // 3 Mb
    public $file_audioFileMaxSize = 1024 * 1024 * 15; // 3 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'content',
                    'type',
                    'subject_topic_id',
                ],
                'required'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [
                [
                    'type',
                    'subject_topic_id',
                ],
                'integer'
            ],
            [
                [
                    'content',
                    'description',
                    'file_url',
                ],
                'string'
            ],
            [['subject_topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['subject_topic_id' => 'id']],
            [['file_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,ppt,pptx,zip', 'maxSize' => $this->file_fileFileMaxSize],
            [['file_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,gimp,bmp,jpeg', 'maxSize' => $this->file_imageFileMaxSize],
            [['file_video'], 'file', 'skipOnEmpty' => true, 'extensions' => 'mp4,avi', 'maxSize' => $this->file_videoFileMaxSize],
            [['file_audio'], 'file', 'skipOnEmpty' => true, 'extensions' => 'mp3,ogg', 'maxSize' => $this->file_audioFileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'type' => 'Type',
            'subject_topic_id' => 'subject_topic_id',
            'description' => 'description',
            'file_url' => "File Url",
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
        $fields = [
            'id',
            'content',
            'type',
            'subject_topic_id',
            'description',
            'file_url',

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
        $extraFields = [
            'subject',
            'subjectTopic',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return Subject::find()->where(['id' => $this->subjectTopic->subject_id])->one();
    }

    /**
     * Gets query for [[Languages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectTopic()
    {
        return $this->hasOne(SubjectTopic::className(), ['id' => 'subject_topic_id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        /* Fayl Yuklash*/
        $model->file_file = UploadedFile::getInstancesByName('file_file');
        $model->type = self::TYPE_TEXT;
        if ($model->file_file) {
            $model->file_file = $model->file_file[0];
            $fileUrl = $model->uploadFile("file_file", $model->subject_topic_id);
            if ($fileUrl) {
                $model->file_url = $fileUrl;
                $model->type = self::TYPE_FILE;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Fayl Yuklash*/

        /* Image Yuklash*/
        $model->file_image = UploadedFile::getInstancesByName('file_image');
        if ($model->file_image) {
            $model->file_image = $model->file_image[0];
            $fileUrl = $model->uploadFile("file_image", $model->subject_topic_id);
            if ($fileUrl) {
                $model->type = self::TYPE_IMAGE;
                $model->file_url = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Image Yuklash*/

        /* Video Yuklash*/
        $model->file_video = UploadedFile::getInstancesByName('file_video');
        if ($model->file_video) {
            $model->file_video = $model->file_video[0];
            $fileUrl = $model->uploadFile("file_video", $model->subject_topic_id);
            if ($fileUrl) {
                $model->file_url = $fileUrl;
                $model->type = self::TYPE_VIDEO;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Video Yuklash*/

        /* Audio Yuklash*/
        $model->file_audio = UploadedFile::getInstancesByName('file_audio');
        if ($model->file_audio) {
            $model->file_audio = $model->file_audio[0];
            $fileUrl = $model->uploadFile("file_audio", $model->subject_topic_id);
            if ($fileUrl) {
                $model->file_url = $fileUrl;
                $model->type = self::TYPE_AUDIO;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Audio Yuklash*/

        if ($model->save()) {
            if (isset($post['order'])) {
                $lastOrder = SubjectContent::find()->where(['subject_topic_id' => $model->subject_topic_id])->orderBy(['order' => SORT_DESC])->select('order')->one();
                if ($lastOrder) {
                    $model->order = $lastOrder->order + 1;
                }
                $model->update();
            }
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

        $model->type = self::TYPE_TEXT;

        /* Fayl Yuklash*/
        $model->file_file = UploadedFile::getInstancesByName('file_file');
        if ($model->file_file) {
            $model->file_file = $model->file_file[0];
            $fileUrl = $model->uploadFile("file_file", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->content);
                $model->file_url = $fileUrl;
                $model->type = self::TYPE_FILE;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Fayl Yuklash*/

        /* Image Yuklash*/
        $model->file_image = UploadedFile::getInstancesByName('file_image');
        if ($model->file_image) {
            $model->file_image = $model->file_image[0];
            $fileUrl = $model->uploadFile("file_image", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->content);
                $model->type = self::TYPE_IMAGE;
                $model->file_url = $fileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Image Yuklash*/

        /* Video Yuklash*/
        $model->file_video = UploadedFile::getInstancesByName('file_video');
        if ($model->file_video) {
            $model->file_video = $model->file_video[0];
            $fileUrl = $model->uploadFile("file_video", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->content);
                $model->file_url = $fileUrl;
                $model->type = self::TYPE_VIDEO;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Video Yuklash*/

        /* Audio Yuklash*/
        $model->file_audio = UploadedFile::getInstancesByName('file_audio');
        if ($model->file_audio) {
            $model->file_audio = $model->file_audio[0];
            $fileUrl = $model->uploadFile("file_audio", $model->subject_topic_id);
            if ($fileUrl) {
                self::deleteFile($model->content);
                $model->file_url = $fileUrl;
                $model->type = self::TYPE_AUDIO;
            } else {
                $errors[] = $model->errors;
            }
        }
        /* Audio Yuklash*/

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
            $this->created_by = Yii::$app->user->identity->getId();
        } else {
            $this->updated_by = Yii::$app->user->identity->getId();
        }
        return parent::beforeSave($insert);
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function typesArray($key = null)
    {
        $array = [
            self::TYPE_TEXT => _e('TEXT'),
            self::TYPE_FILE => _e('FILE'),
            self::TYPE_IMAGE => _e('IMAGE'),
            self::TYPE_VIDEO => _e('VIDEO'),
            self::TYPE_AUDIO => _e('AUDIO'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }

    public function uploadFile($type, $subject_topic_id)
    {
        $subject = SubjectTopic::findOne($subject_topic_id);
        $folder = self::UPLOADS_FOLDER . "/subject_" . $subject->subject_id . "/topic_" . $subject_topic_id . "/";
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . $folder)) {
                mkdir(STORAGE_PATH . $folder, 0777, true);
            }
            if ($this->isNewRecord) {
                $subjectContent = SubjectContent::find()->orderBy('id DESC')->one();
                $fileName = ($subjectContent ? ($subjectContent->id + 1) : 1) . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->$type->extension;
            } else {
                $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->$type->extension;
            }
            $miniUrl = $folder . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->$type->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public static function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH . $oldFile);
            }
        }
        return true;
    }

}
