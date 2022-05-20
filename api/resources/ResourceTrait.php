<?php

namespace api\resources;

use common\models\model\Student;
use common\models\model\TeacherAccess;
use common\models\model\Translate;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

trait ResourceTrait
{

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
            ],
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_on',
                'updatedAtAttribute' => 'updated_on',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    public function loadApi($data)
    {
        return $this->load($data, '');
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get created by
     *
     * @return void
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }


    public static function createFromTable($nameArr, $table_name, $model_id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        foreach ($nameArr as $key => $value) {

            $new_translate[$key] = new Translate();
            $new_translate[$key]->name = $value;
            $new_translate[$key]->table_name = $table_name;
            $new_translate[$key]->model_id = $model_id;
            if ($new_translate[$key]->save()) {
                $transaction->commit();
                return true;
            } else {
                $errors[] = $new_translate[$key]->getErrorSummary(true);
                return simplify_errors($errors);
            }
        }
    }

    public static function teacher_access_user_id($teacher_access_id)
    {
        return TeacherAccess::findOne($teacher_access_id)
            ->user_id ?? null;
    }

    public static function student($type = null, $user_id = null)
    {
        if ($user_id == null) {
            $user_id = current_user_id();
        }
        if ($type == null) {
            $type = 1;
        }
        $student = Student::findOne(['user_id' => $user_id]);
        if ($type == 1) {
            return  $student->id ?? null;
        } elseif ($type == 2) {
            return  $student ?? null;
        }
    }
}
