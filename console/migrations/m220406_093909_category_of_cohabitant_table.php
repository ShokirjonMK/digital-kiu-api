<?php

use yii\db\Migration;

/**
 * Class m220406_093909_category_of_cohabitants_table
 */
class m220406_093909_category_of_cohabitant_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'category_of_cohabitant';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('category_of_cohabitant');
        }

        $this->createTable('{{%category_of_cohabitant}}', [
            'id' => $this->primaryKey(),
//            'name'=>$this->string(255)->notNull(),
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
        $this->dropTable('category_of_cohabitants');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220406_093909_category_of_cohabitants_table cannot be reverted.\n";

        return false;
    }
    */
}
