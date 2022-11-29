<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%oferta}}`.
 */
class m221114_094414_create_oferta_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'oferta';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('oferta');
        }

        $this->createTable('{{%oferta}}', [
            'id' => $this->primaryKey(),
            'role' => $this->json()->notNull(),
            'is_comformed' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'date' => $this->timestamp()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
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
        $this->dropTable('{{%oferta}}');
    }
}
