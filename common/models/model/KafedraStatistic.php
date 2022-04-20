<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;


class KafedraStatistic extends Kafedra
{
    public static $selected_language = 'uz';


    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            // 'questionCount',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'direction',
            'leader',
            'userAccess',

            'faculty',
            'subjects',
            'description',
            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    public function getQuestionAll()
    {
        return $this->hasMany(Question::className(), ['faculty_id' => 'id']);
    }

    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['kafedra_id' => 'id']);
    }
}
