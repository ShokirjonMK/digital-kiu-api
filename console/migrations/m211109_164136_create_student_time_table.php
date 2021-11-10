<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_time}}`.
 */
class m211109_164136_create_student_time_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  
        $this->createTable('{{%student_time_table}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'time_table_id' => $this->integer()->notNull(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('pe_student_time_table_student_id', 'student_time_table', 'student_id', 'student', 'id');
        $this->addForeignKey('ce_student_time_table_time_table_id', 'student_time_table', 'time_table_id', 'time_table', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('pe_student_time_table_student_id', 'student_time_table');
        $this->dropForeignKey('ce_student_time_table_time_table_id', 'student_time_table');
        $this->dropTable('{{%student_time_table}}');
    }
    
}
