<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%teacher_subject_for_content}}`.
 */
class m220823_072021_create_teacher_subject_for_content_table extends Migration
{
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'teacher_subject_for_content';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('teacher_subject_for_content');
        }

        $this->createTable('{{%teacher_subject_for_content}}', [
            'id' => $this->primaryKey(),

            'user_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->notNull(),
            'langs' => $this->json()->Null()->comment(''),
            'lang_id' => $this->integer(11)->null(),

            'type' => $this->tinyInteger(1)->defaultValue(1),


            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);
        $this->createIndex('teacher_subject_for_content__user_id','teacher_subject_for_content','user_id');
        $this->addForeignKey('teacher_subject_for_content__user_id', 'teacher_subject_for_content', 'user_id', 'users', 'id');
        $this->createIndex('teacher_subject_for_content__subject_id', 'teacher_subject_for_content', 'subject_id');
        $this->addForeignKey('teacher_subject_for_content__subject_id', 'teacher_subject_for_content', 'subject_id', 'subject', 'id');
        $this->createIndex('teacher_subject_for_content__lang_id', 'teacher_subject_for_content', 'lang_id');
        $this->addForeignKey('teacher_subject_for_content__lang_id', 'teacher_subject_for_content', 'lang_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('teacher_subject_for_content', 'teacher_subject_for_content');
        $this->dropForeignKey('teacher_subject_for_content', 'teacher_subject_for_content');
        $this->dropForeignKey('teacher_subject_for_content', 'teacher_subject_for_content');

        $this->dropTable('{{%teacher_subject_for_content}}');
    }
}
