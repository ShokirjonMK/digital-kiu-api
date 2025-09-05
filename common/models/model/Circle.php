<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;
use Yii;

class Circle extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const UPLOADS_FOLDER = 'uploads/circle/';

    public $upload_image;
    public $circleImageMaxSize = 1024 * 1024 * 3;  // 3 Mb

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'circle';
    }

    public function rules()
    {
        return [
            [['type', 'finished_status', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['image'], 'string', 'max' => 255],
            [['upload_image'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png,jpg,jpeg,bmp', 'maxSize' => $this->circleImageMaxSize]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => _e('Name'),
            'description' => _e('Description'),
            'type' => _e('Type'),
            'image' => _e('Image'),
            'finished_status' => _e('Finished Status'),
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
            // 'description' => function ($model) {
            //     return $model->translate->description ?? '';
            // },
            'type',
            'image',
            'finished_status',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'circleSchedules',
            'description',

            'schedules',
            'students',
            'countSchedules',
            'countStudents',


            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getSchedules()
    {
        if (isRole('teacher')) {
            return $this->hasMany(CircleSchedule::class, ['circle_id' => 'id'])
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['teacher_user_id' => current_user_id()]);
        }
        // Use relation definition with is_deleted filter for efficiency
        return $this->hasMany(CircleSchedule::class, ['circle_id' => 'id'])
            ->andWhere(['is_deleted' => 0]);
    }

    public function getCircleSchedules()
    {
        if (isRole('teacher')) {
            return $this->hasMany(CircleSchedule::class, ['circle_id' => 'id'])
                ->andWhere(['is_deleted' => 0])
                ->andWhere(['teacher_user_id' => current_user_id()]);
        }
        // Use relation definition with is_deleted filter for efficiency
        return $this->hasMany(CircleSchedule::class, ['circle_id' => 'id'])
            ->andWhere(['is_deleted' => 0]);
    }

    public function getStudents()
    {
        if (isRole('teacher')) {
            return $this->hasMany(CircleStudent::class, ['circle_id' => 'id'])
                ->leftJoin('circle_schedule', 'circle_schedule.id = circle_student.circle_schedule_id')
                ->andWhere(['circle_student.is_deleted' => 0])
                ->andWhere(['circle_schedule.is_deleted' => 0])
                ->andWhere(['circle_schedule.teacher_user_id' => current_user_id()]);
        }

        // Use relation definition with is_deleted filter for efficiency
        return $this->hasMany(CircleStudent::class, ['circle_id' => 'id'])
            ->andWhere(['is_deleted' => 0]);
    }

    public function getCountSchedules()
    {
        // Use COUNT(*) in SQL for performance, avoid loading all models
        return $this->getSchedules()->count();
    }

    public function getCountStudents()
    {
        // Use COUNT(*) in SQL for performance, avoid loading all models
        return $this->getStudents()->count();
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    public function getInfoRelation()
    {
        return $this
            ->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        return $this
            ->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }
        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $has_error = Translate::checkingAll($post);

        $model->upload_image = UploadedFile::getInstancesByName('upload_image');
        if ($model->upload_image) {
            $model->upload_image = $model->upload_image[0];
            $circleImageUrl = $model->uploadFile();
            if ($circleImageUrl) {
                $model->image = $circleImageUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($has_error['status']) {
            if ($model->save()) {
                Translate::createTranslate($post['name'] ?? [], $model->tableName(), $model->id, $post['description'] ?? []);
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

        $model->upload_image = UploadedFile::getInstancesByName('upload_image');
        if ($model->upload_image) {
            $model->upload_image = $model->upload_image[0];
            $circleImageUrl = $model->uploadFile();
            if ($circleImageUrl) {
                $model->image = $circleImageUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['name'])) {
                    Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description'] ?? []);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            return double_errors($errors, $has_error['errors']);
        }
    }

    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = time() . '_' . \Yii::$app->security->generateRandomString(3) . '.' . $this->upload_image->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->upload_image->saveAs($url, false);
            return 'storage/' . $miniUrl;
        } else {
            return false;
        }
    }

    //
    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists(HOME_PATH . $oldFile)) {
                unlink(HOME_PATH . $oldFile);
            }
        }
        return true;
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
