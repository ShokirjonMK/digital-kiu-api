<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%teacher_subject_for_subject}}`.
 */
class m220823_063631_create_teacher_subject_for_subject_table extends Migration
{
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'teacher_subject_for_subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('teacher_subject_for_subject');
        }

        $this->createTable('{{%teacher_subject_for_subject}}', [
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

        $this->addForeignKey('teacher_subject_for_subject_user_id', 'teacher_subject_for_subject', 'user_id', 'users', 'id');
        $this->addForeignKey('teacher_subject_for_subject_subject_id', 'teacher_subject_for_subject', 'subject_id', 'subject', 'id');
        $this->addForeignKey('teacher_subject_for_subject_lang_id', 'teacher_subject_for_subject', 'lang_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('teacher_subject_for_subject_user_id', 'teacher_subject_for_subject');
        $this->dropForeignKey('teacher_subject_for_subject_kpi_category_id', 'teacher_subject_for_subject');
        $this->dropForeignKey('teacher_subject_for_subject_kpi_edu_year_id', 'teacher_subject_for_subject');

        $this->dropTable('{{%teacher_subject_for_subject}}');
    }
}
