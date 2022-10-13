<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test_trigger}}`.
 */
class m221013_062312_create_test_trigger_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'test_trigger';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('test_trigger');
        }

        $this->createTable('{{%test_trigger}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string('255'),
            'description' => $this->string(),

            'count' => $this->integer()->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);


        $this->trigger('test_trigger_no_1');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%test_trigger}}');
    }
}
