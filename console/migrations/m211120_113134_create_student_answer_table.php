<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_answer}}`.
 */
class m211120_113134_create_student_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%student_answer}}', [
            'id' => $this->primaryKey(),
            'file' => $this->string(255)->Null(),
            'exam_id' => $this->integer()->notNull(),
            'exam_question_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'option_id' => $this->integer()->Null(),
            'answer' => $this->text()->Null(),
            'ball' => $this->integer()->Null(),
            'teacher_access_id' => $this->integer()->Null(),
            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),
            'type' => $this->tinyInteger(1)->notNull()->comment("1-savol, 2-test, 3-another"),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student_answer}}');
    }
}
