<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_question}}`.
 */
class m211115_154019_create_exam_question_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_question}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'file' => $this->string(255)->notNull(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('ses_student_exam_student_id', 'student_exam', 'student_id', 'student', 'id');
        $this->addForeignKey('ses_student_exam_teacher_id', 'student_exam', 'teacher_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ses_student_exam_student_id', 'student_exam');
        $this->dropForeignKey('ses_student_exam_teacher_id', 'student_exam');
        $this->dropTable('{{%exam_question}}');
    }
  
}
