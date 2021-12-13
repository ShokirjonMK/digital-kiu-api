<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_semestr_subject".
 *
 * @property int $id
 * @property int $edu_semestr_id
 * @property int $subject_id
 * @property int $subject_type_id
 * @property float $credit
 * @property int $all_ball_yuklama
 * @property int $is_checked
 * @property int $max_ball
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrExamsType[] $eduSemestrExamsTypes
 * @property EduSemestr $eduSemestr
 * @property Subject $subject
 * @property SubjectType $subjectType
 * @property EduSemestrSubjectCategoryTime[] $eduSemestrSubjectCategoryTimes
 */
class EduSemestrSubject extends \yii\db\ActiveRecord
{

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
        return 'edu_semestr_subject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['edu_semestr_id', 'subject_id'], 'required'],
            //    [['edu_semestr_id', 'subject_id', 'subject_type_id', 'credit', 'all_ball_yuklama', 'is_checked', 'max_ball'], 'required'],
            [['edu_semestr_id', 'subject_id', 'subject_type_id', 'all_ball_yuklama', 'is_checked', 'max_ball', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['credit'], 'number'],
            [['edu_semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::className(), 'targetAttribute' => ['subject_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'edu_semestr_id' => 'Edu Semestr ID',
            'subject_id' => 'Subject ID',
            'subject_type_id' => 'Subject Type ID',
            'credit' => 'Credit',
            'all_ball_yuklama' => 'All Ball Yuklama',
            'is_checked' => 'Is Checked',
            'max_ball' => 'Max Ball',
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

            'edu_semestr_id',
            'subject_id',
            'subject_type_id',
            'credit',
            'all_ball_yuklama',
            'is_checked',
            'max_ball',
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
            'eduSemestrExamsTypes',
            'eduSemestr',
            'subject',
            'subjectType',
            'eduSemestrSubjectCategoryTimes',
            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }



    /**
     * Gets query for [[EduSemestrExamsTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrExamsTypes()
    {
        return $this->hasMany(EduSemestrExamsType::className(), ['edu_semestr_subject_id' => 'id']);
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[SubjectType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::className(), ['id' => 'subject_type_id']);
    }

    /**
     * Gets query for [[EduSemestrSubjectCategoryTimes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubjectCategoryTimes()
    {
        return $this->hasMany(EduSemestrSubjectCategoryTime::className(), ['edu_semestr_subject_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        $EduSemestrSubject = EduSemestrSubject::findOne([
            'edu_semestr_id' => $model->edu_semestr_id,
            'subject_id' => $model->subject_id,
        ]);
        if (isset($EduSemestrSubject)) {
            $errors[] = _e('This Edu Subject already exists in This Semester');
            return $errors;
        }
        if ($model->save()) {
            $subjectSillabus = SubjectSillabus::findOne(['subject_id' => $post['subject_id']]);
            $all_ball_yuklama = 0;
            $max_ball = 0;

            if (isset($subjectSillabus)) {

                if (isset($subjectSillabus->edu_semestr_subject_category_times)) {
                    $EduSemestrSubjectCategoryTimes = json_decode(str_replace("'", "", $subjectSillabus->edu_semestr_subject_category_times));
                    foreach ($EduSemestrSubjectCategoryTimes as $subjectCatId => $subjectCatValues) {
                        $EduSemestrSubjectCategoryTime1 = new EduSemestrSubjectCategoryTime();
                        $EduSemestrSubjectCategoryTime1->edu_semestr_subject_id = $model->id;
                        $EduSemestrSubjectCategoryTime1->subject_category_id = $subjectCatId;
                        $EduSemestrSubjectCategoryTime1->hours = $subjectCatValues;
                        $EduSemestrSubjectCategoryTime1->save();
                        $all_ball_yuklama  = $all_ball_yuklama + $subjectCatValues;
                    }
                }

                if (isset($subjectSillabus->edu_semestr_exams_types)) {
                    $EduSemestrExamType = json_decode(str_replace("'", "", $subjectSillabus->edu_semestr_exams_types));
                    foreach ($EduSemestrExamType as $examsTypeId => $examsTypeMaxBal) {
                        $EduSemestrExamsType1 = new EduSemestrExamsType();
                        $EduSemestrExamsType1->edu_semestr_subject_id = $model->id;
                        $EduSemestrExamsType1->exams_type_id = $examsTypeId;
                        $EduSemestrExamsType1->max_ball = $examsTypeMaxBal;
                        $EduSemestrExamsType1->save();
                        $max_ball  = $max_ball + $examsTypeMaxBal;

                        /**
                         *   imtihonlar  imtixon turlari bo'yicha avto yaralishi 
                         */
                        $newExam = new Exam();
                        $newExam->exam_type_id = $examsTypeId;
                        $newExam->edu_semestr_subject_id = $model->id;
                        $newExam->start = date("Y-m-d H:i:s");
                        $newExam->finish = date("Y-m-d H:i:s");
                        $newExam->max_ball = $examsTypeMaxBal;
                        $newExam->min_ball = $examsTypeMaxBal;
                        $newExam->save();
                        /** */
                    }
                }
                $model->all_ball_yuklama = $all_ball_yuklama;
                $model->max_ball = $max_ball;
                $model->subject_type_id = $subjectSillabus->subject_type_id;
                $model->credit = $subjectSillabus->credit;
            }
            $model->update();
            $transaction->commit();
            return true;
        } else {
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
            $all_ball_yuklama = 0;
            $max_ball = 0;
            if (isset($post['SubjectCategory'])) {
                $SubjectCategory = json_decode(str_replace("'", "", $post['SubjectCategory']));
                EduSemestrSubjectCategoryTime::deleteAll(['edu_semestr_subject_id' => $model->id]);
                foreach ($SubjectCategory as $subjectCatId => $subjectCatValues) {
                    $EduSemestrSubjectCategoryTime = new EduSemestrSubjectCategoryTime();
                    $EduSemestrSubjectCategoryTime->edu_semestr_subject_id = $model->id;
                    $EduSemestrSubjectCategoryTime->subject_category_id = $subjectCatId;
                    $EduSemestrSubjectCategoryTime->hours = $subjectCatValues;
                    $EduSemestrSubjectCategoryTime->save();
                    $all_ball_yuklama  = $all_ball_yuklama + $subjectCatValues;
                }
            }
            if (isset($post['EduSemestrExamType'])) {
                $EduSemestrExamType = json_decode(str_replace("'", "", $post['EduSemestrExamType']));
                EduSemestrExamsType::deleteAll(['edu_semestr_subject_id' => $model->id]);

                $oldExamsTypeIds = Exam::find()
                    ->where(['edu_semestr_subject_id' => $model->id])->all();

                foreach ($oldExamsTypeIds as $oldExamsTypeOne) {
                    $oldExamsTypeOne->is_deleted = 1;
                    $oldExamsTypeOne->save();
                }
                foreach ($EduSemestrExamType as $examsTypeId1 => $examsTypeMaxBal1) {
                    $EduSemestrExamsType = new EduSemestrExamsType();
                    $EduSemestrExamsType->edu_semestr_subject_id = $model->id;
                    $EduSemestrExamsType->exams_type_id = $examsTypeId1;
                    $EduSemestrExamsType->max_ball = $examsTypeMaxBal1;
                    $EduSemestrExamsType->save();
                    $max_ball  = $max_ball + $examsTypeMaxBal1;

                    /**
                     *  imtihonlar  imtixon turlari bo'yicha avto yaralishi 
                     */
                    $hasExam = Exam::findOne(['exam_type_id' => $examsTypeId1, 'edu_semestr_subject_id' => $model->id]);
                    if (!isset($hasExam)) {
                        $newExam = new Exam();
                        $newExam->exam_type_id = $examsTypeId1;
                        $newExam->edu_semestr_subject_id = $model->id;
                        $newExam->start = date("Y-m-d H:i:s");
                        $newExam->finish = date("Y-m-d H:i:s");
                        $newExam->max_ball = $examsTypeMaxBal1;
                        $newExam->min_ball = $examsTypeMaxBal1;
                        $newExam->save();
                    } else {
                        $hasExam->is_deleted = 0;
                        $hasExam->start = date("Y-m-d H:i:s");
                        $hasExam->finish = date("Y-m-d H:i:s");
                        $hasExam->max_ball = $examsTypeMaxBal1;
                        $hasExam->min_ball = $examsTypeMaxBal1;
                        $hasExam->save();
                    }
                    /** */
                }
            }
            $model->all_ball_yuklama = $all_ball_yuklama;
            $model->max_ball = $max_ball;
            $model->update();
            $transaction->commit();
            return true;
        } else {

            return simplify_errors($errors);
        }
    }

    public static function deleteItem($model)
    {
        $errors = [];

        EduSemestrSubjectCategoryTime::deleteAll(['edu_semestr_subject_id' => $model->id]);
        EduSemestrExamsType::deleteAll(['edu_semestr_subject_id' => $model->id]);

        if ($model->delete()) {
            return true;
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
