<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%relation}}`.
 */
class m220105_143031_create_relation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_access}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'table_name' => $this->string()->notNull(),
            'table_id' => $this->integer()->notNull(),
            'role_name' => $this->string()->Null(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('rui_user_access_user', 'user_access', 'user_id', 'user', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('rui_user_access_user', 'user_access');


        $this->dropTable('{{%user_access}}');
    }
}
