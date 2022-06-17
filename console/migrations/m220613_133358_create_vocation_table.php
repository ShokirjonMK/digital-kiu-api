<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vocation}}`.
 */
class m220613_133358_create_vocation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%vocation}}', [
            'id' => $this->primaryKey(),

            'start_date' => $this->date()->notNull(),
            'finish_date' => $this->date()->notNull(),
            'symbol' => $this->string(5)->null(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger(2)->defaultValue(1)->comment("1- tatil, 2-kasal, 3-......"),

            'year' => $this->integer()->Null(),
            'month' => $this->integer()->Null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);
        $this->addForeignKey('vu_vocation_user', 'vocation', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('vu_vocation_user', 'vocation');
        $this->dropTable('{{%vocation}}');
    }
}
