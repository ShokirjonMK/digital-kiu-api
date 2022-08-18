<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%content}}`.
 */
class m220818_133001_create_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'content';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('content');
        }
        $this->createTable('{{%content}}', [
            'id' => $this->primaryKey(),
            'description'=> $this->text()->null(),
            'ball'=>$this->double()->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'teacher_access_id' => $this->integer(11)->notNull(),
            'subject_topic_id' => $this->integer(11)->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('content_user_id', 'content', 'user_id', 'users', 'id');
        $this->addForeignKey('content_teacher_access_id', 'content', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('content_subject_topic_id', 'content', 'subject_topic_id', 'subject_topic', 'id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('content_user_id', 'content');
        $this->dropForeignKey('content_teacher_access_id', 'content');
        $this->dropForeignKey('content_subject_topic_id', 'content');

        $this->dropTable('{{%content}}');
    }
}
