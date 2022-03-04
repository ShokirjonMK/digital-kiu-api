<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "Question".
 *
 * @property int $id
 * @property int $student_id
 * @property int $exam_id
 * @property int $teacher_id
 * @property int $ball
 * @property int $attempt
 * @property int $order
 * @property int $status
 * 
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class Question extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    const UPLOADS_FOLDER = 'uploads/question_files/';
    public $question_file;
    public $questionFileMaxSize = 1024 * 1024 * 3; // 3 Mb


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'question';
    }

    /**
     * {@inheritdoc}
     */


    public function rules()
    {
        return [
            [
                [
                    // 'course_id',
                    'semestr_id',
                    'subject_id',
                    // 'ball',
                    'question',
                    'lang_id',
                    'question_type_id'
                ],
                'required'
            ],

            [
                [
                    'course_id',
                    'semestr_id',
                    'subject_id',
                    'lang_id',
                    'level',
                    'question_type_id',
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

            [['file'], 'string', 'max' => 255],
            [['ball'], 'double'],

            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['lang_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['lang_id' => 'id']],
            [['question_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuestionType::className(), 'targetAttribute' => ['question_type_id' => 'id']],
            [['question_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->questionFileMaxSize],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student Id',
            'exam_id' => 'Exam Id',
            'teacher_id' => 'Teacher Id',
            'ball' => 'Ball',
            'attempt' => 'Attempt',
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

            'course_id',
            'semestr_id',
            'subject_id',
            'subQuestion' => function ($model) {
                return $model->subQuestions ?? [];
            },
            'question_file' => function ($model) {
                return $model->file ?? '';
            },
            'options' => function ($model) {
                return $model->options ?? [];
            },
            'file',
            // 'ball',
            'question',
            'lang_id',
            'level',
            'question_type_id',

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
            'subQuestions',

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
     * Gets query for [['Languages']].
     * Languages
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id' => 'lang_id']);
    }

    /**
     * Gets query for [['SubQuestions']].
     * SubQuestions
     * @return \yii\db\ActiveQuery
     */
    public function getSubQuestions()
    {
        return $this->hasMany(SubQuestion::className(), ['question_id' => 'id']);
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

        $subQuestionPescent = 0;

        if ($model->save()) {

            if (isset($post['sub_question'])) {
                if ($post['sub_question'] != null && $post['sub_question'] != "") {
                    $post['sub_question'] = str_replace("'", "", $post['sub_question']);
                    $sub_question = json_decode(str_replace("'", "", $post['sub_question']));

                    foreach ($sub_question as $sub_question_one) {
                        if (isset($sub_question_one->question) && isset($sub_question_one->percent)) {
                            $subQuestionNew = new SubQuestion();
                            $subQuestionNew->question = $sub_question_one->question;
                            $subQuestionNew->percent = $sub_question_one->percent;
                            $subQuestionNew->ball = isset($model->ball) ? ($model->ball * $sub_question_one->percent / 100) : 0;
                            $subQuestionNew->question_id = $model->id;
                            if (!$subQuestionNew->save()) {
                                $errors['subQuestion'][] =  $subQuestionNew->errors;
                            }
                            $subQuestionPescent += $sub_question_one->percent;
                        } else {
                            $errors['subQuestion'] = _e('question and percent are required');
                        }
                    }
                    if ($subQuestionPescent != 100) {
                        $errors[] = _e('Sum of percent(' . $subQuestionPescent . ') of SubQuestion\'s is must be 100%');
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }
            }
            if (count($errors) == 0) {
                $transaction->commit();
                return true;
            }
        }

        $transaction->rollBack();
        return simplify_errors($errors);
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
            $subQuestionPescent = 0;
            if (isset($post['sub_question'])) {
                if ($post['sub_question'] != null && $post['sub_question'] != "") {
                    $post['sub_question'] = str_replace("'", "", $post['sub_question']);
                    $sub_question = json_decode(str_replace("'", "", $post['sub_question']));
                    SubQuestion::deleteAll(['question_id' => $model->id]);

                    foreach ($sub_question as $sub_question_one) {
                        if (isset($sub_question_one->question) && isset($sub_question_one->percent)) {
                            $subQuestionNew = new SubQuestion();
                            $subQuestionNew->question = $sub_question_one->question;
                            $subQuestionNew->percent = $sub_question_one->percent;
                            $subQuestionNew->ball = isset($model->ball) ? ($model->ball * $sub_question_one->percent / 100) : 0;
                            $subQuestionNew->question_id = $model->id;
                            if (!$subQuestionNew->save()) {
                                $errors['subQuestion'][] =  $subQuestionNew->errors;
                            }
                            $subQuestionPescent += $sub_question_one->percent;
                        } else {
                            $errors['subQuestion'] = _e('question and percent are required');
                        }
                    }
                    if ($subQuestionPescent != 100) {
                        $errors[] = _e('Sum of percent(' . $subQuestionPescent . ') of SubQuestion\'s is must be 100%');
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                }
            }
            if (count($errors) == 0) {

                $model->deleteFile($oldFile);
                $transaction->commit();
                return true;
            }
        }

        $transaction->rollBack();
        return simplify_errors($errors);
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
