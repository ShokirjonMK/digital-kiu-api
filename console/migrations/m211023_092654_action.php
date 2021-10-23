<?php

use yii\db\Migration;

/**
 * Class m211023_092654_action
 */
class m211023_092654_action extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('action', [
            'id' => $this->primaryKey(),
            'controller'=>$this->string(255)->notNull(),
            'action'=>$this->string(255)->notNull(),
            'method'=>$this->string(255)->notNull(),
            'user_id'=>$this->integer()->notNull(),


            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->Null(),
            'updated_at'=>$this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);


        $this->addForeignKey('ua_action_user_id','action','user_id','users','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ua_action_user_id','action');
        $this->dropTable('action');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211023_092654_action cannot be reverted.\n";

        return false;
    }
    */
}
