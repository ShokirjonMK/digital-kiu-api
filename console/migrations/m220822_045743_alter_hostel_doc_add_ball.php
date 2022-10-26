<?php

use yii\db\Migration;

/**
 * Class m220822_045743_alter_hostel_doc_add_ball
 */
class m220822_045743_alter_hostel_doc_add_ball extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('hostel_doc', 'ball', $this->double()->null()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220822_045743_alter_hostel_doc_add_ball cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220822_045743_alter_hostel_doc_add_ball cannot be reverted.\n";

        return false;
    }
    */
}
