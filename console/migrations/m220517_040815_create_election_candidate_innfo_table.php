<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%election_candidate_innfo}}`.
 */
class m220517_040815_create_election_candidate_innfo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%election_candidate_innfo}}', [
            'id' => $this->primaryKey(),
            'election_candidate' => $this->integer()->notNull(),


            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('eciec_election_candidate_info_election_candidate_mk', 'election_candidate_info', 'election_candidate_id', 'election_candidate', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eciec_election_candidate_info_election_candidate_mk', 'election_candidate_info');
        $this->dropTable('{{%election_candidate_innfo}}');
    }
}
