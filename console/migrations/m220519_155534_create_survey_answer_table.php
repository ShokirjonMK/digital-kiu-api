<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%survey_answer}}`.
 */
class m220519_155534_create_survey_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'survey_answer';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('survey_answer');
        }

        $this->createTable('{{%survey_answer}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'survey_question_id' => $this->integer()->notNull(),
            'ball' => $this->integer()->notNull(),
            'exam_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'user_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('sasq_survey_answer_survey_question', 'survey_answer', 'survey_question_id', 'survey_question', 'id');
        $this->addForeignKey('saq_survey_answer_subject', 'survey_answer', 'survey_question_id', 'survey_question', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sasq_survey_answer_survey_question', 'survey_answer');
        $this->dropForeignKey('saq_survey_answer_subject', 'survey_answer');

        $this->dropTable('{{%survey_answer}}');
    }
}
