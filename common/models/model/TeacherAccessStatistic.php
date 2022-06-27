<?php

namespace common\models\model;

use Yii;

class TeacherAccessStatistic extends TeacherAccess
{

    public function fields()
    {
        $fields =  [
            'id',
            'subject' => function ($model) {
                return $model->subject->name ?? '';
            },
            // 'examStudent' => function ($model) {
            //     return $model->examStudent ?? 0;
            // },
            'examStudentCount' => function ($model) {
                return $model->examStudentCount ?? 0;
            },
            'exam' => function ($model) {
                return $model->exam->name ?? '';
            },
            'exams' => function ($model) {
                return $model->exams->name ?? '';
            },
            'checkedCount' => function ($model) {
                return $model->checkCount ?? 0;
            },
            'percent' => function ($model) {
                return $model->checkCount ?  ceil($model->checkCount / $model->examStudentCount * 100) : 0;
            },
            'actCount' => function ($model) {
                return $model->actCount ?? 0;
            },
            'notCount' => function ($model) {
                return $model->notCount ?? 0;
            },
            // 'examStudent' => function ($model) {
            //     return $model->examStudent ?? 0;
            // },
            // 'language_id',
            // 'status',
            // 'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',

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
            'checkCount',


            'actCount',
            'notCount',


            'timeTables',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getCheckCount()
    {
        $model = new ExamStudent();
        $query = $model->find();
        $query = $query->andWhere([$model->tableName() . '.teacher_access_id' => $this->id])
            ->leftJoin("exam_student_answer", "exam_student_answer.exam_student_id = " . $model->tableName() . ".id ")
            ->leftJoin("exam_student_answer_sub_question", "exam_student_answer_sub_question.exam_student_answer_id = exam_student_answer.id")
            // ->andWhere(['not', ['esasq.ball' => null, 'esasq.teacher_conclusion' => null]])

            ->andWhere(['IS NOT', 'exam_student_answer_sub_question.ball', null])
            ->andWhere(['IS NOT', 'exam_student_answer_sub_question.teacher_conclusion', null])
            ->groupBy('exam_student.id');

        // dd($query->createCommand()->getRawSql());
        // dd("qweqwe");
        // return 122;
        return count($query->all());
    }

    public function getExamStudent()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }

    public function getExamStudentAct()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id'])->onCondition(['act' => 1]);
    }

    public function getExamStudentCount()
    {
        return count($this->examStudent);
    }

    public function getActCount()
    {
        return count($this->examStudentAct);
    }

    public function getExam()
    {
        return $this->examStudent[0]->exam;
    }
    public function getExams()
    {
        return $this->examStudentOne->exam;
    }

    public function getExamStudentOne()
    {
        return $this->hasOne(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }


    public function getCheckedCount()
    {
        $model = new ExamStudent();

        $query = $model->find();
        $query->andWhere([$model->tableName() . 'teacher_access_id' => $this->id]);
        $query->leftJoin('exam_student_answer esa', 'esa.exam_student_id = ' . $model->tableName() . '.id');
        $query->andWhere(['>=', 'esa.ball', 0]);

        dd($query->createCommand()->rawSql());
        $query->all();

        return $query->count();
    }
}
