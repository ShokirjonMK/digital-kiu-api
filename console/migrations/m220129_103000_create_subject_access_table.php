<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_access}}`.
 */
class m220129_103000_create_subject_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subject_access}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->text()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('sas_subject_access_subject_id_bm', 'subject_access', 'subject_id', 'subject', 'id');
        $this->addForeignKey('sau_user_subject_id_mb', 'subject_access', 'user_id', 'users', 'id');

    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sas_subject_access_subject_id_bm', 'subject_topic');
        $this->dropForeignKey('sau_user_subject_id_mb', 'subject_topic');
        $this->dropTable('{{%subject_access}}');
    }
}
