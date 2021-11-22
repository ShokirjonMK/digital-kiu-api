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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_teacher_check}}');
    }
}
