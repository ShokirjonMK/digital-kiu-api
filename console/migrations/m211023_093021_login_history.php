<?php

use yii\db\Migration;

/**
 * Class m211023_093021_login_history
 */
class m211023_093021_login_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('login_history', [
            'id' => $this->primaryKey(),
            'ip'=>$this->string(255)->notNull(),
            'user_id'=>$this->integer()->notNull(),
            'device'=>$this->string(255)->notNull(),
            'device_id'=>$this->string(255)->notNull(),
            'type'=>$this->string(255)->notNull(),
            'model_device'=>$this->string(255)->notNull(),


            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->Null(),
            'updated_at'=>$this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);


        $this->addForeignKey('ul_login_history_user_id','login_history','user_id','users','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ul_login_history_user_id','login_history');
        $this->dropTable('login_history');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211023_093021_login_history cannot be reverted.\n";

        return false;
    }
    */
}
