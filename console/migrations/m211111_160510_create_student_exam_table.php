<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_exam}}`.
 */
class m211111_160510_create_student_exam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%student_exam}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'exam_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->Null(),
            'ball' => $this->integer()->defaultValue(0),
            'attempt' => $this->integer()->defaultValue(1),

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
        $this->dropTable('{{%student_exam}}');
    }
}
