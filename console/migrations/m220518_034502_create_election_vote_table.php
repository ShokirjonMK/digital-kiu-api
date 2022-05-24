<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%election_vote}}`.
 */
class m220518_034502_create_election_vote_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'election_vote';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('election_vote');
        }

        $this->createTable('{{%election_vote}}', [
            'id' => $this->primaryKey(),
            'election_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'election_candidate_id' => $this->integer()->notNull(),



            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('eve_election_vote_election_id', 'election_vote', 'election_id', 'election', 'id');
        $this->addForeignKey('evu_election_vote_user_id', 'election_vote', 'user_id', 'users', 'id');
        $this->addForeignKey('evec_election_vote_election_candidate', 'election_vote', 'election_candidate_id', 'election_candidate', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eve_election_vote_election_id', 'election_vote');
        $this->dropForeignKey('evu_election_vote_user_id', 'election_vote');
        $this->dropForeignKey('evec_election_vote_election_candidate', 'election_vote');
        $this->dropTable('{{%election_vote}}');
    }
}
