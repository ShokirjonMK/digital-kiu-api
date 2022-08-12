<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_topic_reference}}`.
 */
class m220812_061953_create_subject_topic_reference_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'subject_topic_reference';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_topic_reference');
        }

        $this->createTable('{{%subject_topic_reference}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'subject_topic_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->notNull(),

            'link' => $this->string(255)->notNull(),
            'name' => $this->text()->notNull(),
            'start_page' => $this->integer()->notNull(),
            'end_page' => $this->integer()->notNull(),


            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);
        $this->addForeignKey('str_subject_topic_reference_subject_id', 'subject_topic_reference', 'subject_id', 'subject', 'id');
        $this->addForeignKey('str_subject_topic_reference_subject_topic_id', 'subject_topic_reference', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('str_subject_topic_reference_user_id', 'subject_topic_reference', 'user_id', 'users', 'id');
        $this->addForeignKey('str_subject_topic_reference_teacher_access_id', 'subject_topic_reference', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('str_subject_topic_reference_subject_id', 'subject_topic_reference');
        $this->dropForeignKey('str_subject_topic_reference_subject_topic_id', 'subject_topic_reference');
        $this->dropForeignKey('str_subject_topic_reference_user_id', 'subject_topic_reference');
        $this->dropForeignKey('str_subject_topic_reference_teacher_access_id', 'subject_topic_reference');
        $this->dropTable('{{%subject_topic_reference}}');
    }
}
