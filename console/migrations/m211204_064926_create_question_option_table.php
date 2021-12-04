<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%question_option}}`.
 */
class m211204_064926_create_question_option_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%question_option}}', [
            'id' => $this->primaryKey(),

            'question_id' => $this->integer()->notNull(),
            'file' => $this->string(255)->Null(),
            'is_correct' => $this->tinyInteger(1)->defaultValue(0),
            'option' => $this->text()->notNull(),

            'order' => $this->tinyInteger(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

         $this->addForeignKey('qoq_question_option_question', 'question_option', 'question_id', 'question', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropForeignKey('qoq_question_option_question', 'question_option');

        $this->dropTable('{{%question_option}}');
    }
}
