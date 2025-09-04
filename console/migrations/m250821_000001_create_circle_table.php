<?php

use yii\db\Migration;

class m250821_000001_create_circle_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }


        $tableName = Yii::$app->db->tablePrefix . 'circle';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('circle');
        }             


        $this->createTable('circle', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger(1)->defaultValue(1),
            'image' => $this->string(255)->null(),
            'finished_status' => $this->tinyInteger(1)->defaultValue(0),  // 0 bo'lsa tamomlanmagan qilishga ruxsat yo'q 1 bo'lsa bor
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx_circle_type', 'circle', 'type');
        $this->createIndex('idx_circle_status', 'circle', 'status');
        $this->createIndex('idx_circle_is_deleted', 'circle', 'is_deleted');
        $this->createIndex('idx_circle_created_at', 'circle', 'created_at');
        $this->createIndex('idx_circle_updated_at', 'circle', 'updated_at');
        $this->createIndex('idx_circle_created_by', 'circle', 'created_by');
        $this->createIndex('idx_circle_updated_by', 'circle', 'updated_by');
    }

    public function safeDown()
    {
        $this->dropIndex('idx_circle_type', 'circle');
        $this->dropIndex('idx_circle_status', 'circle');
        $this->dropIndex('idx_circle_is_deleted', 'circle');
        $this->dropIndex('idx_circle_created_at', 'circle');
        $this->dropIndex('idx_circle_updated_at', 'circle');
        $this->dropIndex('idx_circle_created_by', 'circle');
        $this->dropIndex('idx_circle_updated_by', 'circle');
        $this->dropTable('circle');
    }
}

