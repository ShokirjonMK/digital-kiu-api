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
            'questionsCount' => function ($model) {
                return $model->questionsCount ?? 0;
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


            'subjectsCount',
            'questions',
            'questionsCount',



            'description',
            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }

    public function getQuestions()
    {
        return Question::find()
            // ->select('id')
            ->where([
                'in', 'subject_id',
                Subject::find()->where(['kafedra_id' => $this->id])->select('id')
            ])
            ->all();
    }


    public function getQuestionsCount()
    {
        return count($this->questions);
    }

    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['kafedra_id' => 'id']);
    }

    public function getSubjectsCount()
    {
        return count($this->subjects);
    }
}
