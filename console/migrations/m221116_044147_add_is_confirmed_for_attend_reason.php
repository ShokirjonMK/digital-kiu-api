<?php

use yii\db\Migration;

/**
 * Class m221116_044147_add_is_confirmed_for_attend_reason
 */
class m221116_044147_add_is_confirmed_for_attend_reason extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('attend_reason', 'is_confirmed', $this->integer()->defaultValue(0)->after('id'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221116_044147_add_is_confirmed_for_attend_reason cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221116_044147_add_is_confirmed_for_attend_reason cannot be reverted.\n";

        return false;
    }
    */
}
