<?php

use yii\db\Migration;

/**
 * Class m220823_103528_teacher_subject_content
 */
class m220823_103528_teacher_content extends Migration
{
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'teacher_subject_content';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('teacher_subject_content');
        }

        $this->createTable('{{%teacher_subject_content}}', [
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
        
        $this->addForeignKey('teacher_subject_content__user_id', 'teacher_subject_content', 'user_id', 'users', 'id');
        $this->addForeignKey('teacher_subject_content__subject_id', 'teacher_subject_content', 'subject_id', 'subject', 'id');
        $this->addForeignKey('teacher_subject_content__lang_id', 'teacher_subject_content', 'lang_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('teacher_subject_content', 'teacher_subject_content');
        $this->dropForeignKey('teacher_subject_content', 'teacher_subject_content');
        $this->dropForeignKey('teacher_subject_content', 'teacher_subject_content');

        $this->dropTable('{{%teacher_subject_content}}');
    }
}
