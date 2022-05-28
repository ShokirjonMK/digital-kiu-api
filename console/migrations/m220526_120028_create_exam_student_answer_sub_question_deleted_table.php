<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_answer_sub_question_deleted}}`.
 */
class m220526_120028_create_exam_student_answer_sub_question_deleted_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_student_answer_sub_question_deleted';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_answer_sub_question_deleted');
        }

        $this->createTable('{{%exam_student_answer_sub_question_deleted}}', [
            'id' => $this->primaryKey(),

            'file' => $this->string(255)->Null(),
            'exam_student_answer_id' => $this->integer()->Null(),
            'exam_student_answer_sub_question_id' => $this->integer()->Null(),
            'sub_question_id' => $this->integer()->Null(),
            'teacher_conclusion' => $this->text()->Null(),
            'answer' => $this->text()->Null(),
            'ball' => $this->double()->Null(),
            'max_ball' => $this->double()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->Null()->defaultValue(0),
            'updated_by' => $this->integer()->Null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->Null()->defaultValue(0),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student_answer_sub_question_deleted}}');
    }
}
