<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%instruction}}`.
 */
class m220630_101634_create_inctruction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'instruction';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('instruction');
        }
        $this->createTable('{{%instruction}}', [
            'id' => $this->primaryKey(),
            'file_url' => $this->string(255),
            'key' => $this->string(255)->null()->unique(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%instruction}}');
    }
}
