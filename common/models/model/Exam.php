<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "exam".
 *
 * @property int $id
 * @property int $exam_type_id
 * @property int $edu_semestr_subject_id
 * @property string $start
 * @property string $finish
 * @property float|null $max_ball
 * @property float|null $min_ball
 * @property int|null $order
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrSubject $eduSemestrSubject
 * @property ExamsType $examType
 * @property ExamQuestion[] $examQuestions
 * @property ExamStudentAnswer[] $examStudentAnswers
 */
class Exam extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE_FOR_ALL = 1;
    const STATUS_INACTIVE = 9;
    const STATUS_FINISHED = 2;

    const PROTECTED_TURE = 1;
    const PROTECTED_FALSE = 0;




    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exam_type_id', 'edu_semestr_subject_id', 'start', 'finish'], 'required'],
            [['exam_type_id', 'is_protected', 'duration', 'edu_semestr_subject_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['start', 'finish'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['max_ball', 'min_ball'], 'number'],
            [['question_count_by_type'], 'safe'],
            [['edu_semestr_subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']],
            [['exam_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExamsType::className(), 'targetAttribute' => ['exam_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name in translate
            'question_count_by_type' => 'Question Count By Type',
            'exam_type_id' => 'Exam Type ID',
            'edu_semestr_subject_id' => 'Edu Semestr Subject ID',
            'start' => 'Start',
            'finish' => 'Finish',
            'is_protected' => 'Is Protected',
            'duration' => 'Duration',
            'max_ball' => 'Max Ball',
            'min_ball' => 'Min Ball',
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
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'question_count_by_type',
            'exam_type_id',
            'edu_semestr_subject_id',
            'start',
            'finish',
            'duration',
            'is_protected',
            'max_ball',
            'min_ball',
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
            'eduSemestrSubject',
            'examType',
            'examQuestions',
            'examStudentAnswers',
            'description',
            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    /**
     * Gets query for [[EduSemestrSubject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    /**
     * Gets query for [[ExamType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamType()
    {
        return $this->hasOne(ExamsType::className(), ['id' => 'exam_type_id']);
    }

    /**
     * Gets query for [[ExamQuestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamQuestions()
    {
        return $this->hasMany(ExamQuestion::className(), ['exam_id' => 'id']);
    }

    /**
     * Gets query for [[ExamStudentAnswers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExamStudentAnswers()
    {
        return $this->hasMany(ExamStudentAnswer::className(), ['exam_id' => 'id']);
    }


    public static function generatePasswords($post)
    {
        $errors = [];
        $examId = isset($post['exam_id']) ?  $post['exam_id'] : null;

        if (isset($examId)) {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                $eduSemestrSubject = EduSemestrSubject::findOne($exam->edu_semestr_subject_id);
                if (isset($eduSemestrSubject)) {
                    $studentTimeTable = StudentTimeTable::find()
                        // ->select(['student_time_table.id as id', 'student_time_table.student_id as student_id', 'tt.language_id as lang_id'])
                        ->leftJoin("time_table tt", "tt.id = student_time_table.time_table_id")
                        ->where([
                            'tt.edu_semester_id' => $eduSemestrSubject->edu_semestr_id,
                            'tt.subject_id' => $eduSemestrSubject->subject_id,
                        ])
                        ->all();

                    foreach ($studentTimeTable as $studentTimeTableOne) {
                        $student_id = $studentTimeTableOne->student_id;
                        $langId = $studentTimeTableOne->timeTable->language_id;

                        $ExamStudentHas = ExamStudent::find()->where([
                            'exam_id' => $examId,
                            'student_id' => $student_id,
                        ])
                            ->orderBy('id desc')
                            ->one();

                        if (isset($ExamStudentHas)) {
                            $ExamStudent = $ExamStudentHas;
                        } else {
                            $ExamStudent = new ExamStudent();
                        }

                        $ExamStudent->exam_id = $examId;
                        $ExamStudent->student_id = $student_id;
                        $ExamStudent->lang_id = $langId;
                        $ExamStudent->password = _random_string('numeric', 4);
                        // $ExamStudent->attempt = isset($ExamStudentHas) ? $ExamStudentHas->attempt + 1 : 1;
                        $ExamStudent->status = ExamStudentAnswer::STATUS_NEW;
                        $ExamStudent->save(false);
                    }
                    ////
                } else {
                    $errors[] = _e("This subject does not belongs to this smester");
                }
            } else {
                $errors[] = _e("Exam not found");
            }
        } else {
            $errors[] = _e("Exam Id is required");
        }
        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }

    public static function getPasswords($post)
    {
        $errors = [];
        $data = [];
        $data['is_ok'] = false;
        $examId = $post['exam_id'];

        if (isset($examId)) {
            $exam = Exam::findOne($examId);
            if (isset($exam)) {
                $examStudents = ExamStudent::find()
                    ->where(['exam_id' => $examId])
                    ->leftJoin("student std", "std.id = exam_student.student_id")
                    ->orderBy('std.direction_id')
                    ->all();

                foreach ($examStudents as $examStudentOne) {
                    $oneStd = [];
                    $oneStd['full_name'] = Profile::getFullname($examStudentOne->student->profile);
                    $oneStd['direction'] = $examStudentOne->student->direction->translate->name;
                    $oneStd['password'] = $examStudentOne->password;
                    $data['students'][] = $oneStd;
                }
                $eduSemestrSubject = EduSemestrSubject::findOne($exam->edu_semestr_subject_id);
                if (isset($eduSemestrSubject)) {
                    $info = [];
                    $info['subject'] = $eduSemestrSubject->subject->translate->name;
                    $info['start'] = $exam->start;
                    $info['finish'] = $exam->finish;
                    $info['exam_type'] = $exam->examType->translate->name;

                    $data['info'] = $info;
                } else {
                    $errors[] = _e("This subject does not belongs to this smester");
                }
                $data['is_ok'] = true;

                return $data;
            } else {
                $errors[] = _e("Exam not found");
            }
        } else {
            $errors[] = _e("Exam Id is required");
        }

        if (count($errors) > 0) {
            return $errors;
        } else {
            return $data;
        }
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (isset($post['question_count_by_type'])) {
            $post['question_count_by_type'] = str_replace("'", "", $post['question_count_by_type']);
            if (!isJsonMK($post['question_count_by_type'])) {
                $json_errors['question_count_by_type'] = [_e('Must be Json')];
            }

            foreach (array_unique((array)json_decode($post['question_count_by_type'])) as $questionTypeId => $questionTypeCount) {

                $q = QuestionType::findOne($questionTypeId);
                if (!(isset($q))) {
                    $errors[] = _e("Question Type Id (" . $questionTypeId . ") not found");
                }
            }

            if (count($errors) > 0) {
                return $errors;
            }

            $model->question_count_by_type = json_encode(array_unique((array)json_decode($post['question_count_by_type'])));
        }

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {
            if (isset($post['duration'])) {
                $model->duration =  str_replace("'", "", $post['duration']);
                $model->duration =  str_replace('"', "", $model->duration);
                $duration = explode(":", $model->duration);
                $hours = isset($duration[0]) ? $duration[0] : 0;
                $min = isset($duration[1]) ? $duration[1] : 0;
                $model->duration = (int)$hours * 3600 + (int)$min * 60;
            }

            // $model->duration = strtotime($model->finish) - strtotime($model->start);
            // $model->duration = ($model->duration > 0) ? $model->duration : 0;
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                return simplify_errors($errors);
            }
        } else {
            return double_errors($errors, $has_error['errors']);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if (isset($post['question_count_by_type'])) {
            $post['question_count_by_type'] = str_replace("'", "", $post['question_count_by_type']);
            if (!isJsonMK($post['question_count_by_type'])) {
                $json_errors['question_count_by_type'] = [_e('Must be Json')];
            }

            foreach (array_unique((array)json_decode($post['question_count_by_type'])) as $questionTypeId => $questionTypeCount) {

                $q = QuestionType::findOne($questionTypeId);
                if (!(isset($q))) {
                    $errors[] = _e("Question Type Id (" . $questionTypeId . ") not found");
                }
            }

            if (count($errors) > 0) {
                return $errors;
            }

            $model->question_count_by_type = json_encode(array_unique((array)json_decode($post['question_count_by_type'])));
        }

        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
            if (isset($post['duration'])) {
                $model->duration =  str_replace("'", "", $post['duration']);
                $model->duration =  str_replace('"', "", $model->duration);
                $duration = explode(":", $model->duration);
                $hours = isset($duration[0]) ? $duration[0] : 0;
                $min = isset($duration[1]) ? $duration[1] : 0;
                $model->duration = (int)$hours * 3600 + (int)$min * 60;
            }

            if ($model->save()) {
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
                $transaction->commit();
                return true;
            } else {

                return simplify_errors($errors);
            }
        } else {
            return double_errors($errors, $has_error['errors']);
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
