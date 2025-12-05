<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%circle_open}}`.
 */
class m251205_063239_create_circle_open_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }


        $tableName = Yii::$app->db->tablePrefix . 'circle_open';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('circle_open');
        }


        $this->createTable('{{%circle_open}}', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull()->comment('course id'),
            'smena' => $this->tinyInteger(1)->notNull()->comment('1 - day, 2 - evening, 3 - night'),

            'circle_kuz_from' => $this->string(16)->null()->comment('Fall selection start (format mm-dd HH:ii:ss)'),
            'circle_kuz_to' => $this->string(16)->null()->comment('Fall selection end (format mm-dd HH:ii:ss)'),
            'circle_bahor_from' => $this->string(16)->null()->comment('Spring selection start (format mm-dd HH:ii:ss)'),
            'circle_bahor_to' => $this->string(16)->null()->comment('Spring selection end (format mm-dd HH:ii:ss)'),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('fk_circle_open_course', 'circle_open', 'course_id', 'course', 'id');
        $this->createIndex('idx_circle_open_course_id', 'circle_open', 'course_id');
        $this->createIndex('idx_circle_open_smena', 'circle_open', 'smena');
        $this->createIndex('idx_circle_open_status', 'circle_open', 'status');
        $this->createIndex('idx_circle_open_is_deleted', 'circle_open', 'is_deleted');
        $this->createIndex('idx_circle_open_created_at', 'circle_open', 'created_at');
        $this->createIndex('idx_circle_open_updated_at', 'circle_open', 'updated_at');
        $this->createIndex('idx_circle_open_created_by', 'circle_open', 'created_by');
        $this->createIndex('idx_circle_open_updated_by', 'circle_open', 'updated_by');

        $this->createIndex('idx_circle_open_circle_kuz_from', 'circle_open', 'circle_kuz_from');
        $this->createIndex('idx_circle_open_circle_kuz_to', 'circle_open', 'circle_kuz_to');
        $this->createIndex('idx_circle_open_circle_bahor_from', 'circle_open', 'circle_bahor_from');
        $this->createIndex('idx_circle_open_circle_bahor_to', 'circle_open', 'circle_bahor_to');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_circle_open_course', 'circle_open');
        $this->dropIndex('idx_circle_open_course_id', 'circle_open');
        $this->dropIndex('idx_circle_open_smena', 'circle_open');
        $this->dropIndex('idx_circle_open_status', 'circle_open');
        $this->dropIndex('idx_circle_open_is_deleted', 'circle_open');
        $this->dropIndex('idx_circle_open_created_at', 'circle_open');
        $this->dropIndex('idx_circle_open_updated_at', 'circle_open');
        $this->dropIndex('idx_circle_open_created_by', 'circle_open');
        $this->dropIndex('idx_circle_open_updated_by', 'circle_open');
        $this->dropIndex('idx_circle_open_circle_kuz_from', 'circle_open');
        $this->dropIndex('idx_circle_open_circle_kuz_to', 'circle_open');
        $this->dropIndex('idx_circle_open_circle_bahor_from', 'circle_open');
        $this->dropIndex('idx_circle_open_circle_bahor_to', 'circle_open');
        $this->dropTable('{{%circle_open}}');
    }
}
