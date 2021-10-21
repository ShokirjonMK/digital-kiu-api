<?php

use yii\db\Migration;

/**
 * Class m211012_134519_building
 */
class m211012_134519_building extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('building', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(255)->notNull(),


            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('building');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_134519_building cannot be reverted.\n";

        return false;
    }
    */
}
