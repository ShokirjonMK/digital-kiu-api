<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_teacher_check}}`.
 */
class m211120_113736_create_exam_teacher_check_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_teacher_check}}', [
            'id' => $this->primaryKey(),

            'teacher_access_id' => $this->integer()->Null(),
            'student_id' => $this->integer()->notNull(),
            'exam_id' => $this->integer()->notNull(),

            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('etchs_exam_teacher_check_teacher_access', 'exam_student', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('etchs_exam_teacher_check_student', 'exam_student', 'student_id', 'student', 'id');
        $this->addForeignKey('etchs_exam_teacher_check_exam', 'exam_student', 'exam_id', 'student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('etchs_exam_teacher_check_student', 'exam_student');
        $this->dropForeignKey('etchs_exam_teacher_check_exam', 'exam_student');
        $this->dropForeignKey('etchs_exam_teacher_check_teacher_access', 'exam_student');

        $this->dropTable('{{%exam_teacher_check}}');
    }
}
