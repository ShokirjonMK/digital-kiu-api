<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%election}}`.
 */
class m220514_052345_create_election_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%election}}', [
            'id' => $this->primaryKey(),
            // 'start' => $this->dateTime()->notNull(),
            // 'finish' => $this->dateTime()->notNull(),
            'start' => $this->integer()->defaultValue(0),
            'finish' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
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
        $this->dropTable('{{%election}}');
    }
}
