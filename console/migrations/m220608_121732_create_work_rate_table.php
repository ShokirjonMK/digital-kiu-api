<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%work_rate}}`.
 */
class m220608_121732_create_work_rate_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'work_rate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('work_rate');
        }

        $this->createTable('{{%work_rate}}', [
            'id' => $this->primaryKey(),

            'hour' => $this->double()->defaultValue(0),
            'rate' => $this->double()->defaultValue(0),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
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
        $this->dropTable('{{%work_rate}}');
    }
}
