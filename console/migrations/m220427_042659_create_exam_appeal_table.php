<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_appeal}}`.
 */
class m220427_042659_create_exam_appeal_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    /* public function safeUp()
    {
        $this->createTable('{{%exam_appeal}}', [
            'id' => $this->primaryKey(),

            'student_id' => $this->integer()->notNull(),



            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('pe_student_time_table_student_id', 'student_time_table', 'student_id', 'student', 'id');
    } */

    /**
     * {@inheritdoc}
     */
    /* public function safeDown()
    {
        $this->dropForeignKey('pe_student_time_table_student_id', 'student_time_table');

        $this->dropTable('{{%exam_appeal}}');
    } */
}
