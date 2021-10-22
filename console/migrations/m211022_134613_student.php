<?php

use yii\db\Migration;

/**
 * Class m211022_134613_student
 */
class m211022_134613_student extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('student');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211022_134613_student cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_134613_student cannot be reverted.\n";

        return false;
    }
    */
}
