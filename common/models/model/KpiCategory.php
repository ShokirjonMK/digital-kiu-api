<?php

namespace common\models\model;

use api\resources\ResourceTrait;
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
 */
class KpiCategory extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'term',
                    'tab',
                ], 'integer'
            ],
            [
                [
                    'max_ball',
                ], 'double'
            ],
            // [
            //     [
            //         'fields',
            //     ], 'text', 'max' => 255
            // ],

            // [
            //     [
            //         'fields',
            //     ], 'json'
            // ],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['status'], 'default', 'value' => 1],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // name on info table
            // description on info table
            // tab name on info table

            'fields',
            'max_ball',
            'term',
            'tab',

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
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->info->name ?? '';
            },

            'fields',
            'max_ball',
            'termName',
            'tab',

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
            'term',
            'tabName',
            'extra',

            'kpiData',
            'kpiMark',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getInfo()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->info->description ?? '';
    }

    public function getTabName()
    {
        return $this->info->tab_name ?? '';
    }

    public function getTermName()
    {
        return $this->term()[$this->term] ?? '';
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(KpiCategoryInfo::class, ['kpi_category_id' => 'id'])
            ->andOnCondition(['lang' => Yii::$app->request->get('lang')]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(KpiCategoryInfo::class, ['kpi_category_id' => 'id'])
            ->andOnCondition(['lang' => self::$selected_language]);
    }

    /**
     * Get all of the getKpiData for the KpiCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getKpiData()
    {
        // return 1;
        $edu_year_id = EduYear::findOne(['year' => date("Y")])->id;
        return $this->hasOne(KpiData::className(), ['kpi_category_id' => 'id'])->onCondition(['is_deleted' => 0, 'edu_year_id' => $edu_year_id, 'user_id' => Yii::$app->request->get('user_id') ?? current_user_id()]);
    }

    public function getKpiMark()
    {
        return $this->hasMany(KpiMark::className(), ['kpi_category_id' => 'id'])->onCondition(['is_deleted' => 0, 'user_id' => Yii::$app->request->get('user_id') ?? current_user_id()]);
    }


    public function getKpiCategory()
    {
        return $this->hasMany(KpiMark::class, ['kpi_category_id' => 'id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }

        if (($post['fields'][0] == "'") && ($post['fields'][strlen($post['fields']) - 1] == "'")) {
            $post['fields'] =  substr($post['fields'], 1, -1);
        }

        if (!isJsonMK($post['fields'])) {
            $errors['fields'] = [_e('Must be Json')];
        } else {
            $fields = ((array)json_decode($post['fields']));
            // dd($fields);
            if (!empty(array_diff($fields, self::categoryFields()))) {
                $errors['fields'] = [_e('Incorrect fields')];
            } else {
                $model->fields = $fields;
            }
        }


        if ($model->save()) {
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];
                } else {
                    foreach ($post['name'] as $lang => $name) {
                        $info = new KpiCategoryInfo();
                        $info->kpi_category_id = $model->id;
                        $info->lang = $lang;
                        $info->name = $name;
                        $info->description = $post['description'][$lang] ?? null;
                        $info->tab_name = $post['tab_name'][$lang] ?? null;
                        if (!$info->save()) {
                            $errors[] = $info->getErrorSummary(true);
                        }
                    }
                }
            } else {
                $errors[] = [_e('Please send at least one name attribute.')];
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

        if (($post['fields'][0] == "'") && ($post['fields'][strlen($post['fields']) - 1] == "'")) {
            $post['fields'] =  substr($post['fields'], 1, -1);
        }

        if (!isJsonMK($post['fields'])) {
            $errors['fields'] = [_e('Must be Json')];
        } else {
            $model->fields = ((array)json_decode($post['fields']));
        }

        if ($model->save()) {
            if (isset($post['name'])) {
                if (!is_array($post['name'])) {
                    $errors[] = [_e('Please send Name attribute as array.')];
                } else {
                    foreach ($post['name'] as $lang => $name) {
                        $info = KpiCategoryInfo::find()->where(['kpi_category_id' => $model->id, 'lang' => $lang])->one();
                        if ($info) {
                            $info->name = $name;
                            $info->description = $post['description'][$lang] ?? null;
                            $info->tab_name = $post['tab_name'][$lang] ?? null;
                            if (!$info->save()) {
                                $errors[] = $info->getErrorSummary(true);
                            }
                        } else {
                            $info = new KpiCategoryInfo();
                            $info->kpi_category_id = $model->id;
                            $info->lang = $lang;
                            $info->name = $name;
                            $info->description = $post['description'][$lang] ?? null;
                            $info->tab_name = $post['tab_name'][$lang] ?? null;
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

    public static function categoryFields()
    {
        return [
            "date",
            "file",
            // "subject_category",
            // "count_of_copyright",
            "link",
            "input",

        ];

        /*  return
            [
                "input"=> ,
                "link",
                "number",
                "file",
                "date",
                "double_date",
                "select",
                "nimadir"
            ]; */
    }

    public static function term()
    {
        return
            [
                1 => _e("1 year"),
                2 => _e("6 month"),
                3 => _e('Bir kalendar yil davomida'),
                4 => _e('Sertifikat muddati davomida'),
                5 => _e('Taribdan chiqarilgunga qadar'),
            ];
    }

    public static function tab()
    {
        return [
            1 => _e("O‘quv va o‘quv-uslubiy ishlar"),
            2 => _e("Ilmiy va innovatsiyalarga oid ishlar"),
            3 => _e("Xalqaro hamkorlikka oid ishlar"),
            4 => _e("Ma'naviy-ma'rifiy ishlarga rioya etish holati")
        ];
    }

    public function getExtra()
    {
        return self::extra();
    }

    public static function extra()
    {
        return ["fields" => self::categoryFields(), "term" => self::term(), "tab" => self::tab()];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
