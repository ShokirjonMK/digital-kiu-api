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
        $this->createTable('{{%election_candidate}}', [
            'id' => $this->primaryKey(),
            'election_id' => $this->integer()->notNull(),
            

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('eec_election_candidate_election_mk', 'election_candidate', 'election_id', 'election', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eec_election_candidate_election_mk', 'election_candidate');
        $this->dropTable('{{%election_candidate}}');
    }
}
