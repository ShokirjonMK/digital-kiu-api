<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_mark}}`.
 */
class m221227_044617_create_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_mark';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_mark');
        }

        $this->createTable('{{%student_mark}}', [
            'id' => $this->primaryKey(),

            'student_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->notNull(),
            'course_id' => $this->integer(11)->null(),
            'semestr_id' => $this->integer(11)->null(),

            'edu_year_id' => $this->integer(11)->null(),



            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('mark_student_mark_student_id', 'student_mark', 'student_id', 'student', 'id');
        $this->addForeignKey('mark_student_mark_subject_id', 'student_mark', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mark_student_mark_edu_year_id', 'student_mark', 'edu_year_id', 'edu_year', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sto_student_order_student_id', 'student_order');

        $this->dropTable('{{%student_mark}}');
    }
}
