<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_student}}`.
 */
class m211105_151833_create_alter_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `edu_len_id` INT NOT NULL AFTER `description`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_student}}');
    }
}
