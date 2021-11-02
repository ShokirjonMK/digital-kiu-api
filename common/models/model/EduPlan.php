<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "edu_plan".
 *
 * @property int $id
 * @property int $course
 * @property int $semestr
 * @property int $edu_year_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $edu_type_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction $direction
 * @property EduYear $eduYear
 * @property Faculty $faculty
 * @property EduType $eduType
 * @property EduSemestr[] $eduSemestrs
 */
class EduPlan extends \yii\db\ActiveRecord
{

    use ResourceTrait;

    public static $selected_language = 'uz';

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
        return 'edu_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['course', 'semestr', 'edu_year_id', 'faculty_id', 'direction_id', 'edu_type_id', 'fall_start', 'fall_end', 'spring_start', 'spring_end'], 'required'],
            [['course', 'semestr', 'edu_year_id', 'faculty_id', 'direction_id', 'edu_type_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['fall_start', 'fall_end', 'spring_start', 'spring_end'], 'safe'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course' => 'Course',
            'semestr' => 'Semestr',
            'edu_year_id' => 'Edu Year ID',
            'faculty_id' => 'Faculty ID',
            'direction_id' => 'Direction ID',
            'edu_type_id' => 'Edu Type ID',
            'order' => 'Order',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'is_deleted' => 'Is Deleted',
            'fall_start' => 'fall smester start date',
            'fall_end' => 'fall smester end date',
            'spring_start' => 'spring smester start date',
            'spring_end' => 'spring smester end date',
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'faculty_id',
            'semestr',
            'edu_year_id',
            'direction_id',
            'edu_type_id',
            'course',
            'semestr',
            'fall_start',
            'fall_end',
            'spring_start',
            'spring_end',
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
            'direction',
            'eduYear',
            'faculty',
            'eduType',
            'eduSemestrs',
            'description',

            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    /**
     * For tranlating 
     */
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
     * For tranlating 
     */


    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[EduSemestrs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrs()
    {
        return $this->hasMany(EduSemestr::className(), ['edu_plan_id' => 'id']);
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
                return simplify_errors($errors);
            }
        } else {

            $errors[] = $has_error['errors'];
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

        $has_error = Translate::checkingAll($post);
        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                }
                $transaction->commit();
                return true;
            } else {
                return simplify_errors($errors);
            }
        } else {
            return simplify_errors($has_error['errors']);
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
