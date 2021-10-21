<?php

use yii\db\Migration;

/**
 * Class m211021_115327_profile
 */
class m211021_115327_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('profile');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211021_115327_profile cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211021_115327_profile cannot be reverted.\n";

        return false;
    }
    */
}
