<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%election}}`.
 */
class m220514_052345_create_election_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'election';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('election');
        }

        $this->createTable('{{%election}}', [
            'id' => $this->primaryKey(),
            // 'start' => $this->dateTime()->notNull(),
            // 'finish' => $this->dateTime()->notNull(),
            'roles' => $this->string(255)->defaultValue(0),
            'start' => $this->integer()->Null(),
            'finish' => $this->integer()->Null(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
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
        $this->dropTable('{{%election}}');
    }
}
