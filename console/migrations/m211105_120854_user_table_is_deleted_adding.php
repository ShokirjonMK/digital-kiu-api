<?php

use yii\db\Migration;

/**
 * Class m211105_120854_user_table_is_deleted_adding
 */
class m211105_120854_user_table_is_deleted_adding extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `users` CHANGE `deleted` `is_deleted` INT NOT NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211105_120854_user_table_is_deleted_adding cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211105_120854_user_table_is_deleted_adding cannot be reverted.\n";

        return false;
    }
    */
}
