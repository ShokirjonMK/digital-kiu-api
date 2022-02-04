<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%notification_user}}`.
 */
class m220204_050751_create_notification_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%notification_user}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('nuu_notification_user_user_id', 'notification_user', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('nuu_notification_user_user_id', 'notification_user');
        $this->dropTable('{{%notification_user}}');
    }
}
