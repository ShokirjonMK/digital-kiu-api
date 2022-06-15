<?php

namespace common\models\model;

use Yii;

class TeacherAccessStatistic extends TeacherAccess
{

    public function fields()
    {
        $fields =  [
            'id',
            // 'teacher' => function ($model) {
            //     return $model->teacher ?? [];
            // },
            'user_id',
            'subject_id',
            'language_id',
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
            'languages',
            'subject',
            'teacher',
            'user',
            'timeTables',
            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }
}
