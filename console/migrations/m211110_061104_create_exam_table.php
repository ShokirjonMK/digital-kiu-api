<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam}}`.
 */
class m211110_061104_create_exam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam}}', [
            'id' => $this->primaryKey(),
            // nama translate da bo'ladi
            'exam_type_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'start' => $this->dateTime()->notNull(),
            'finish' => $this->dateTime()->notNull(),
            'max_ball' => $this->integer()->defaultValue(0),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('eet_exam_exam_type_id', 'exam', 'exam_type_id', 'exams_type', 'id');
        $this->addForeignKey('eess_exam_edu_semestr_subject_id', 'exam', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('eet_exam_exam_type_id', 'exam');
        $this->dropForeignKey('eess_exam_edu_semestr_subject_id', 'exam');
        $this->dropTable('{{%exam}}');
    }
}
