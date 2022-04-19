<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use yii\behaviors\TimestampBehavior;



class ExamNoStudent extends ExamStudent
{
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
            'exam_id',
            'lang_id',
            'teacher_access_id',
            'ball',

            'attempt',
            'conclusion',
            'plagiat_file',
            'plagiat_percent',

        ];

        return $fields;
    }
}
