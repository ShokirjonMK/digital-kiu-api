<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "Exam".
 *
 * @property int $id
 * @property int $course_id
 * @property int $semestr_id
 * @property int $subject_id
 * @property int $file
 * @property int $ball
 * @property int $question
 * @property int $lang_id
 * @property int $level
 * @property int $question_type_id
 *
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class SubQuestion extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

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
        return 'sub_question';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [
                [
                    'question',
                    'question_id',
                    'percent',
                    'ball',
                ],
                'required'
            ],
            [
                [
                    'question_id',
                    'percent',

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
            [['ball'], 'double'],

            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Question::className(), 'targetAttribute' => ['question_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'question' => 'Question',
            'question_id' => 'Question Id',
            'percent' => 'Percent',
            'ball' => 'Ball',

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

            'question',
            'question_id',
            'percent',
            'ball',
            

            // 'order',
            // 'status',
            // 'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'course',
            'semestr',
            'options',
            'subject',
            'lang',
            'questionType',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [['Course ']].
     * Course
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [['Semestr ']].
     * Semestr
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }


    public function getOptions()
    {
        return $this->hasMany(QuestionOption::className(), ['question_id' => 'id']);
    }

    /**
     * Gets query for [['Subject ']].
     * Subject
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }
    /**
     * Gets query for [['QuestionType ']].
     * QuestionType
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionType()
    {
        return $this->hasOne(QuestionType::className(), ['id' => 'question_type_id']);
    }

    /**
     * Gets query for [['Languages ']].
     * Languages
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        // question file saqlaymiz
        $model->question_file = UploadedFile::getInstancesByName('question_file');
        if ($model->question_file) {
            $model->question_file = $model->question_file[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->file = $questionFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

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

        $oldFile = $model->file;
        // question file saqlaymiz
        $model->question_file = UploadedFile::getInstancesByName('question_file');
        if ($model->question_file) {
            $model->question_file = $model->question_file[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->file = $questionFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        if ($model->save()) {
            $model->deleteFile($oldFile);
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

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            1 => _e('Active'),
            0 => _e('Inactive'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }


    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }
            if ($this->isNewRecord) {
                $fileName = Question::find()->count() + 1 . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->question_file->extension;
            } else {
                $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->question_file->extension;
            }
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->question_file->saveAs($url, false);
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
}
