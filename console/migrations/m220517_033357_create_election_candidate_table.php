<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%election_candidate}}`.
 */
class m220517_033357_create_election_candidate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'election_candidate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('election_candidate');
        }

        $this->createTable('{{%election_candidate}}', [
            'id' => $this->primaryKey(),
            'election_id' => $this->integer()->notNull(),
            'photo' => $this->string(255)->null(),
            'short_info' => $this->text()->null(),
            'full_info' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('ece_election_candidate_election_mk', 'election_candidate', 'election_id', 'election', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ece_election_candidate_election_mk', 'election_candidate');
        $this->dropTable('{{%election_candidate}}');
    }
}
