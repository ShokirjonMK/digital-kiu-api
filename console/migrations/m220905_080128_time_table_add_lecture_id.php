<?php

use yii\db\Migration;

/**
 * Class m220905_080128_time_table_add_lecture_id
 */
class m220905_080128_time_table_add_lecture_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('time_table', 'lecture_id', $this->integer()->null()->after('parent_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220905_080128_time_table_add_lecture_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220905_080128_time_table_add_lecture_id cannot be reverted.\n";

        return false;
    }
    */
}
