<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_type".
 *
 * @property int $id
 * 
 * @property int $subject_id
 * @property int $subject_type_id
 * @property string $edu_semestr_exams_types
 * @property string $edu_semestr_subject_category_times
 * @property int $all_ball_yuklama
 * @property int $max_ball
 * @property double $credit
 
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
class SubjectSillabus extends \yii\db\ActiveRecord
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
        return 'subject_sillabus';
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
                    'subject_type_id',
                    'edu_semestr_exams_types',
                    'edu_semestr_subject_category_times'
                ],
                'required'
            ],
            [
                [
                    'all_ball_yuklama',
                    'max_ball',
                    'credit',
                ],
                'double'
            ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [
                [
                    'subject_id',
                    'subject_type_id',
                ],
                'integer'
            ],
            [
                [
                    'edu_semestr_exams_types',
                    'edu_semestr_subject_category_times',
                ],
                'string'
            ],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject_id' => 'Subject Id',
            'subject_type_id' => 'Subject Type Id',
            'edu_semestr_exams_types' => 'Edu Semestr Exams Types',
            'edu_semestr_subject_category_times' => 'Edu Semestr Subject Category Times',
            'all_ball_yuklama' => 'All Ball Yuklama',
            'max_ball' => 'Max Ball',
            'credit' => 'Credit',

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
            'subject_type_id',
            'edu_semestr_exams_types',
            'edu_semestr_subject_category_times',
            'all_ball_yuklama',
            'max_ball',
            'credit',

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
            'subject',
            'subjectType',

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
        return $this->hasOne(Subject::className(), ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[subjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::className(), ['subject_type_id' => 'id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            return $errors;
        }

        $oldSubjectSillabus = SubjectSillabus::findOne(['subject_id' => $post['subject_id']]);
        if (isset($oldSubjectSillabus)) {
            $errors[] = [_e('This Sillabus already created!')];
            return $errors;
        }

        $json_errors = [];
        $post['edu_semestr_exams_types'] = str_replace("'", "", $post['edu_semestr_exams_types']);
        if (!isJsonMK($post['edu_semestr_exams_types'])) {
            $json_errors['edu_semestr_exams_types'] = [_e('Must be Json')];
        }

        $post['edu_semestr_subject_category_times'] = str_replace("'", "", $post['edu_semestr_subject_category_times']);
        if (!isJsonMK($post['edu_semestr_subject_category_times'])) {
            $json_errors['edu_semestr_subject_category_times'] = [_e('Must be Json')];
        }

        if (count($json_errors)) {
            $errors[] = $json_errors;
        }

     /*    $edu_semestr_exams_typesMODEL = new EduSemestrExamsType();
        //  [['edu_semestr_subject_id', 'exams_type_id', 'max_ball'], 'required']
        foreach (json_decode($post['edu_semestr_exams_types']) as $examsTypeId => $examsTypeMaxBal) {
            // $edu_semestr_exams_typesMODEL->edu_semestr_subject_id = $post['subject_id'];
            $edu_semestr_exams_typesMODEL->exams_type_id = $examsTypeId;
            $edu_semestr_exams_typesMODEL->max_ball = $examsTypeMaxBal;

            if (!$edu_semestr_exams_typesMODEL->validate()) {
              //  $errors[] = $edu_semestr_exams_typesMODEL->errors;
            }
        }
 */
    /*     $edu_semestr_subject_category_timesMODEL = new EduSemestrSubjectCategoryTime();
        //  [['edu_semestr_subject_id', 'subject_category_id', 'hours'], 'required'],
        foreach (json_decode($post['edu_semestr_subject_category_times']) as $subjectCatId => $subjectCatValues) {
            // $edu_semestr_subject_category_timesMODEL->edu_semestr_subject_id = $post['subject_id'];
            $edu_semestr_subject_category_timesMODEL->subject_category_id = $subjectCatId;
            $edu_semestr_subject_category_timesMODEL->hours = $subjectCatValues;sss

            if (!$edu_semestr_subject_category_timesMODEL->validate()) {
               // $errors[] = $edu_semestr_subject_category_timesMODEL->errors;
            }
        } */

        $model->edu_semestr_subject_category_times = $post['edu_semestr_subject_category_times'];
        $model->edu_semestr_exams_types = $post['edu_semestr_exams_types'];

        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                return simplify_errors($errors);
            }
        } else {
            $errors[] = count($errors);
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
        $json_errors = [];

        if (isset($post['edu_semestr_exams_types'])) {
            $post['edu_semestr_exams_types'] = str_replace("'", "", $post['edu_semestr_exams_types']);
            if (!isJsonMK($post['edu_semestr_exams_types'])) {
                $json_errors['edu_semestr_exams_types'] = [_e('Must be Json')];
            } else {
                /* $edu_semestr_exams_typesMODEL = new EduSemestrExamsType();
                //  [['edu_semestr_subject_id', 'exams_type_id', 'max_ball'], 'required']
                foreach (json_decode($post['edu_semestr_exams_types']) as $examsTypeId => $examsTypeMaxBal) {
                    $edu_semestr_exams_typesMODEL->edu_semestr_subject_id = $model->subject_id;
                    $edu_semestr_exams_typesMODEL->exams_type_id = $examsTypeId;
                    $edu_semestr_exams_typesMODEL->max_ball = $examsTypeMaxBal;

                    if (!$edu_semestr_exams_typesMODEL->validate()) {
                        $errors[] = $edu_semestr_exams_typesMODEL->errors;
                    }
                } */

                $model->edu_semestr_exams_types = $post['edu_semestr_exams_types'];
            }
        }


        if (isset($post['edu_semestr_subject_category_times'])) {
            $post['edu_semestr_subject_category_times'] = str_replace("'", "", $post['edu_semestr_subject_category_times']);
            if (!isJsonMK($post['edu_semestr_subject_category_times'])) {
                $json_errors['edu_semestr_subject_category_times'] = [_e('Must be Json')];
            } else {
               /*  $edu_semestr_subject_category_timesMODEL = new EduSemestrSubjectCategoryTime();
                //  [['edu_semestr_subject_id', 'subject_category_id', 'hours'], 'required'],
                foreach (json_decode($post['edu_semestr_subject_category_times']) as $subjectCatId => $subjectCatValues) {
                    $edu_semestr_subject_category_timesMODEL->edu_semestr_subject_id = $model->subject_id;
                    $edu_semestr_subject_category_timesMODEL->subject_category_id = $subjectCatId;
                    $edu_semestr_subject_category_timesMODEL->hours = $subjectCatValues;

                    if (!$edu_semestr_subject_category_timesMODEL->validate()) {
                        $errors[] = $edu_semestr_subject_category_timesMODEL->errors;
                    }
                } */

                $model->edu_semestr_subject_category_times = $post['edu_semestr_subject_category_times'];
            }
        }

        if (count($json_errors)) {
            $errors[] = $json_errors;
        }

        if (count($errors) == 0) {
            if ($model->save()) {
                $transaction->commit();
                return true;
            } else {
                return simplify_errors($errors);
            }
        } else {
            $errors[] = count($errors);
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
}
