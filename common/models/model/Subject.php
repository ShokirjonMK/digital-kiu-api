<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\model\SubjectTopic;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $name
 * @property int $kafedra_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrSubject[] $eduSemestrSubjects
 * @property Kafedra $kafedra
 * @property TeacherAccess[] $teacherAccesses
 * @property TimeTable[] $timeTables
 */
class Subject extends \yii\db\ActiveRecord
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
        return 'subject';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['kafedra_id', 'semestr_id'], 'required'],
            [['kafedra_id', 'semestr_id', 'parent_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],

            [['kafedra_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kafedra::className(), 'targetAttribute' => ['kafedra_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
            'kafedra_id' => 'Kafedra ID',
            'semestr_id' => 'Semestr ID',
            'parent_id' => 'Parent ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            // 'lang' => function ($model) {
            //     return Yii::$app->request->get('lang');
            // },
            'kafedra_id',
            'semestr_id',
            'parent_id',
            'is_deleted',
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
            'subjectSillabus',
            'semestr',
            'child',
            'parent',
            'timeTables',
            'timeTableCount',
            'teacherAccesses',
            'kafedra',
            'semestrSubjects',
            'description',

            'exam',
            'examCount',

            'examStudentByLang',
            'eduSemestrSubjects',

            'questions',
            'questionsCount',
            'questionsByLang',

            'questionUzCount',
            'questionEngCount',
            'questionRuCount',

            'hasContent',
            'topics',

            'surveyAnswers',
            'surveyAnswersSum',
            'surveyAnswersCount',
            'surveyAnswerAverage',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getExam()
    {
        return Exam::find()->where([
            'in', 'edu_semestr_subject_id',
            EduSemestrSubject::find()
                ->where(['subject_id' => $this->id])
                ->select('id')
                ?? 0
        ])->all();
    }

    public function getQuestionsByLang()
    {
        return [
            "UZ"    => ['count' => count($this->questionUz)],
            "ENG"   => [count($this->questionEng)],
            "RU"    => [count($this->questionRu)],

        ];
    }

    public  function getQuestionUzCount()
    {
        return count($this->questionUz);
    }
    public  function getQuestionEngCount()
    {
        return count($this->questionEng);
    }

    public  function getQuestionRuCount()
    {
        return count($this->questionRu);
    }

    public  function getHasContent()
    {
        $model = new SubjectContent();
        $query = $model->find()->where([$this->table_name . '.is_deleted' => 0])->andWhere([$this->table_name . '.archived' => 0]);

        $query = $query->andWhere([
            'in', $model->tableName() . '.subject_topic_id',
            SubjectTopic::find()->select('id')->where(['subject_id' => $this->id])
        ]);

        if (count($query->all()) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public  function getQuestionUz()
    {
        $model = new Question();
        $query = $model->find();

        $query = $query->andWhere(['subject_id' => $this->id]);
        $query = $query->andWhere(['lang_id' => 1]);
        $query = $query->andWhere(['is_deleted' => 0]);
        $query = $query->andWhere(['archived' => 0]);
        return $query->all();
    }

    public  function getQuestionEng()
    {
        $model = new Question();
        $query = $model->find();

        $query = $query->andWhere(['subject_id' => $this->id]);
        $query = $query->andWhere(['lang_id' => 2]);
        $query = $query->andWhere(['is_deleted' => 0]);
        $query = $query->andWhere(['archived' => 0]);

        return $query->all();
    }

    public  function getQuestionRu()
    {
        $model = new Question();
        $query = $model->find();

        $query = $query->andWhere(['subject_id' => $this->id]);
        $query = $query->andWhere(['lang_id' => 3]);
        $query = $query->andWhere(['is_deleted' => 0]);
        $query = $query->andWhere(['archived' => 0]);

        return $query->all();
    }

    public function getTopics()
    {
        return $this->hasMany(SubjectTopic::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSurveyAnswers()
    {
        return $this->hasMany(SurveyAnswer::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSurveyAnswersSum()
    {
        return $this->hasMany(SurveyAnswer::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0])->sum('ball');
    }

    public function getSurveyAnswersCount()
    {
        return $this->hasMany(SurveyAnswer::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0])->count();
    }

    public function getSurveyAnswerAverage()
    {
        if ($this->surveyAnswersCount > 0) {
            // return (float) $this->surveyAnswersSum / $this->surveyAnswersCount;
            return round(((float) $this->surveyAnswersSum / $this->surveyAnswersCount), 2);
        }
        return 0;
    }

    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getQuestionActive()
    {
        return $this->hasMany(Question::className(), ['subject_id' => 'id'])->onCondition(['status' => 1, 'is_deleted' => 0, 'archived' => 0]);
    }

    public function getQuestionsCount()
    {
        return count($this->questions);
    }

    public function getExamStudentByLang()
    {
        return ExamForSubject::find()->where(['edu_semestr_subject_id' => $this->eduSemestrSubject->id ?? 0])->all();
    }

    public function getExamCount()
    {
        return count($this->exam);
    }


    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['subject_id' => 'id']);
    }

    public function getSubject()
    {
        return $this->eduSemestrSubject->subject->name ?? "";
    }


    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
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


    /**
     * Gets query for [[EduSemestrSubjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubjects()
    {
        return $this->hasMany(EduSemestrSubject::className(), ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Kafedra]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKafedra()
    {
        return $this->hasOne(Kafedra::className(), ['id' => 'kafedra_id']);
    }


    /**
     * Gets query for [[Semestr]].
     *semestr_id
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[Parent]].
     *parent_id
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Subject::className(), ['id' => 'parent_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[Child]].
     *child
     * @return \yii\db\ActiveQuery
     */
    public function getChild()
    {
        return $this->hasMany(Subject::className(), ['id' => 'parent_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[TeacherAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherAccesses()
    {
        return $this->hasMany(TeacherAccess::className(), ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[TimeTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTables()
    {
        return $this->hasMany(TimeTable::className(), ['subject_id' => 'id']);
    }
    public function getTimeTableCount()
    {
        return count($this->timeTables);
    }

    /**
     * Gets query for [[SubjectSillabus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubjectSillabus()
    {
        return $this->hasOne(SubjectSillabus::className(), ['subject_id' => 'id']);
    }

    public function getName()
    {
        return $this->translate->name ?? '';
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $has_error = Translate::checkingAll($post);

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
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

        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
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
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
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
