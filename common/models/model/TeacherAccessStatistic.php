<?php

namespace common\models\model;

use Yii;

class TeacherAccessStatistic extends TeacherAccess
{

    public function fields()
    {
        $fields =  [
            'id',
            'subject_id',
            'subject' => function ($model) {
                return $model->subject->name ?? '';
            },
            'examStudentCount' => function ($model) {
                return $model->examStudentCount ?? 0;
            },
            'checkedCount' => function ($model) {
                return $model->checkedCount ?? 0;
            },
            // 'examStudent' => function ($model) {
            //     return $model->examStudent ?? 0;
            // },
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

            'examStudentCount',
            'examStudent',
            'checkedCount',



            'timeTables',
            'createdBy',
            'updatedBy',
        ];

        return $extraFields;
    }


    public function getExamStudent()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }

    public function getExamStudentCount()
    {
        return count($this->examStudent);
    }

    public function getCheckedCount()
    {
        $model = new ExamStudent();

        $query = $model->find();
        $query->andWhere([$model->tableName() . 'teacher_access_id' => $this->id]);
        $query->leftJoin('exam_student_answer esa', 'esa.exam_student_id = ' . $model->tableName() . '.id');
        $query->andWhere(['>=', 'esa.ball', 0]);
        $query->all();

        return $query->count();
    }
}
