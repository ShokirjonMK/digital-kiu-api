<?php

use yii\db\Migration;

/**
 * Class m211012_120438_subject
 */
class m211012_120438_subject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('subject', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(255)->notNull(),
            'kafedra_id'=>$this->integer()->notNull(),


            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('ks_subject_kafedra_id','subject','kafedra_id','kafedra','id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ks_subject_kafedra_id','subject');
        $this->dropTable('subject');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_120438_subject cannot be reverted.\n";

        return false;
    }
    */
}
