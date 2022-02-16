<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_answer}}`.
 */
class m211120_113134_create_student_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_student_answer}}', [
            'id' => $this->primaryKey(),
            'file' => $this->string(255)->Null(),
            'exam_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'option_id' => $this->integer()->Null(),
            'answer' => $this->text()->Null(),
            'ball' => $this->integer()->Null(),
            'teacher_access_id' => $this->integer()->Null(),
            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),
            'type' => $this->tinyInteger(1)->notNull()->comment("1-savol, 2-test, 3-another"),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('ses_exam_student_answer_exam', 'exam_student_answer', 'exam_id', 'exam', 'id');
        $this->addForeignKey('ses_exam_student_answer_exam_question', 'exam_student_answer', 'question_id', 'question', 'id');
        $this->addForeignKey('ses_exam_student_answer_student', 'exam_student_answer', 'student_id', 'student', 'id');
        $this->addForeignKey('ses_exam_student_answer_option', 'exam_student_answer', 'option_id', 'question_option', 'id');
        $this->addForeignKey('ses_exam_student_answer_teacher_access', 'exam_student_answer', 'teacher_access_id', 'teacher_access', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('ses_exam_student_answer_exam', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_exam_question', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_student', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_option', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_teacher_access', 'exam_student_answer');


        $this->dropTable('{{%exam_student_answer}}');
    }
}
