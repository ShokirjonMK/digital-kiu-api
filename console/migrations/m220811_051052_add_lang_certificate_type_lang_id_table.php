<?php

use yii\db\Migration;

/**
 * Class m220811_051052_add_lang_certificate_type_lang_id_table
 */
class m220811_051052_add_lang_certificate_type_lang_id_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //   addColumn('')
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220811_051052_add_lang_certificate_type_lang_id_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220811_051052_add_lang_certificate_type_lang_id_table cannot be reverted.\n";

        return false;
    }
    */
}
