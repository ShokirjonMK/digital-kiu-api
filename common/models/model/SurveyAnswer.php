<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "direction".
 *
 * @property int $id
 * @property string $name
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
class SurveyAnswer extends \yii\db\ActiveRecord
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
        return 'survey_answer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'subject_id',
                    'survey_question_id',
                    'ball',
                    'exam_id',
                    'edu_semestr_subject_id',
                    'student_id',
                    'user_id',
                ], 'integer'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status', 'type'], 'default', 'value' => 1],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['survey_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => SurveyQuestion::className(), 'targetAttribute' => ['survey_question_id' => 'id']],
            [['exam_id'], 'exist', 'skipOnError' => true, 'targetClass' => Exam::className(), 'targetAttribute' => ['exam_id' => 'id']],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //            'name' => 'Name',

            'subject_id' => _e('subject_id'),
            'survey_question_id' => _e('survey_question_id'),
            'ball' => _e('ball'),
            'exam_id' => _e('exam_id'),
            'edu_semestr_subject_id' => _e('edu_semestr_subject_id'),
            'student_id' => _e('student_id'),
            'user_id' => _e('user_id'),


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
        $fields =  [
            'id',


            'subject_id',
            'survey_question_id',
            'ball',
            'exam_id',
            // 'edu_semestr_subject_id',
            // 'student_id',
            // 'user_id',

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

            'description',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }



    public function getKafedras()
    {
        return $this->hasMany(Kafedra::className(), ['direction_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }

        if ($model->save()) {
            if (isset($post['question'])) {
                if (!is_array($post['question'])) {
                    $errors[] = [_e('Please send Question attribute as array.')];
                } else {
                    foreach ($post['question'] as $lang => $question) {
                        $info = new SurveyQuestionInfo();
                        $info->survey_question_id = $model->id;
                        $info->lang = $lang;
                        $info->question = $question;
                        $info->description = $post['description'][$lang] ?? null;
                        if (!$info->save()) {
                            $errors[] = $info->getErrorSummary(true);
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one Question attribute.')];
            }
        } else {
            $errors[] = $model->getErrorSummary(true);
        }
        if (count($errors) == 0) {
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

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }


        if ($model->save()) {
            if (isset($post['question'])) {
                if (!is_array($post['question'])) {
                    $errors[] = [_e('Please send Question attribute as array.')];
                } else {
                    foreach ($post['question'] as $lang => $question) {
                        $info = SurveyQuestionInfo::find()->where(['survey_question_id' => $model->id, 'lang' => $lang])->one();
                        if ($info) {
                            $info->question = $question;
                            $info->description = $post['description'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        } else {
                            $info = new SurveyQuestionInfo();
                            $info->survey_question_id = $model->id;
                            $info->lang = $lang;
                            $info->question = $question;
                            $info->description = $post['description'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one Question attribute.')];
            }
        } else {
            $errors[] = $model->getErrorSummary(true);
        }
        if (count($errors) == 0) {
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
