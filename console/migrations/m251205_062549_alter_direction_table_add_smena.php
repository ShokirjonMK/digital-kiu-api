<?php

use yii\db\Migration;

class m251205_062549_alter_direction_table_add_smena extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `direction` ADD `smena` INT(11) NULL COMMENT '1 - day, 2 - evening, 3 - night';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE `direction` DROP `smena`;");
        echo "m251205_062549_alter_direction_table_add_smena cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251205_062549_alter_direction_table_add_smena cannot be reverted.\n";

        return false;
    }
    */
}
