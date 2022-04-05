<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\enums\Gender;
use Yii;

class FacultyStatistic extends Faculty
{
    public static $selected_language = 'uz';


    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'studentsCount' => function ($model) {
                return $model->studentsCount ?? [];
            },

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'studentsCountByGender',
            'studentsAll',
            'genderMale',
            'genderFemale',

        ];

        return $extraFields;
    }

    public function getStudentsAll()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id']);
    }

    public function getStudentsCountByGender()
    {
        // return "asss";
        return [
            Gender::MALE => [
                'count' => count($this->genderMale),
                'name' =>  _e('Male')
            ],
            Gender::FEMALE => [
                'count' => count($this->genderFemale),
                'name' =>  _e('FEMALE')
            ],

        ];
    }


    public function getGenderMale()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id'])->andOnCondition(['gender' => 1]);
    }

    public function getGenderFemale()
    {
        return $this->hasMany(StudentStatistic::className(), ['faculty_id' => 'id'])->andOnCondition(['gender' => 0]);
    }


    public function getStudentsCount()
    {
        // return   $this->studentsAll;

        return count($this->studentsAll);
    }
}
