<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student}}`.
 */
class m211111_160510_create_student_exam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_student}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'exam_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->Null(),
            'ball' => $this->double()->defaultValue(0),
            
            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),
            'act' => $this->integer()->defaultValue(0)->comment("1 act tuzilgan imtihon qodalarini bizgan"),

            //'exam_question' => $this->text()->Null()->comment("JSON formatda: question_id, option_id, ball, is_correct,"),
            // 'file' => $this->string(255)->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('ses_exam_student_student_id', 'exam_student', 'student_id', 'student', 'id');
        $this->addForeignKey('ses_exam_student_exam_id', 'exam_student', 'exam_id', 'exam', 'id');
        $this->addForeignKey('ses_exam_student_teacher_access_id', 'exam_student', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ses_exam_student_student_id', 'exam_student');
        $this->dropForeignKey('ses_exam_student_exam_id', 'exam_student');
        $this->dropForeignKey('ses_exam_student_teacher_access_id', 'exam_student');
        $this->dropTable('{{%exam_student}}');
    }
}
