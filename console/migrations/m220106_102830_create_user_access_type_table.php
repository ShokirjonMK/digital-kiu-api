<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access_type}}`.
 */
class m220106_102830_create_user_access_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_access_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->insert('user_access_type', [
            'name' => 'Faculty',
            'url' => 'faculties',
        ]);
        $this->insert('user_access_type', [
            'name' => 'Kafedra',
            'url' => 'kafedras',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_access_type}}');
    }
}
