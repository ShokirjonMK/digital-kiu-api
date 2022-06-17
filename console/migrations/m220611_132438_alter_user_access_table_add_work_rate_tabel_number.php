<?php

use yii\db\Migration;

/**
 * Class m220611_132438_alter_user_access_table_add_work_rate_tabel_number
 */
class m220611_132438_alter_user_access_table_add_work_rate_tabel_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->execute("ALTER TABLE `user_access` ADD `work_rate_id` int null COMMENT 'work_rate';");
        // $this->execute("ALTER TABLE `user_access` ADD `tabel` int null COMMENT 'work_rate';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220611_132438_alter_user_access_table_add_work_rate_tabel_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220611_132438_alter_user_access_table_add_work_rate_tabel_number cannot be reverted.\n";

        return false;
    }
    */
}
