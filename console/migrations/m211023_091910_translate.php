<?php

use yii\db\Migration;

/**
 * Class m211023_091910_translate
 */
class m211023_091910_translate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('translate', [
        'id' => $this->primaryKey(),
        'name'=>$this->string(255)->notNull(),
        'table_name'=>$this->string(255)->notNull(),
        'languages_id'=>$this->integer()->notNull(),


        'order'=>$this->tinyInteger(1)->defaultValue(1),
        'status' => $this->tinyInteger(1)->defaultValue(1),
        'created_at'=>$this->integer()->Null(),
        'updated_at'=>$this->integer()->Null(),
        'created_by' => $this->integer()->notNull()->defaultValue(0),
        'updated_by' => $this->integer()->notNull()->defaultValue(0),
        'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
    ]);


        $this->addForeignKey('lt_translate_language_id','translate','languages_id','languages','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('lt_translate_language_id','translate');
        $this->dropTable('translate');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211023_091910_translate cannot be reverted.\n";

        return false;
    }
    */
}
