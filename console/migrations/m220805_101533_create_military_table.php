<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%military}}`.
 */
class m220805_101533_create_military_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'military';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('military');
        }
        $this->createTable('{{%military}}', [
            'id' => $this->primaryKey(),
            'joy' => $this->string(255)->Null(),
            'chas_raqami' => $this->string(10)->Null(),
            'year' => $this->string(11)->Null(),
            'seria_raqami'=>$this->string(33)->Null(),
            'student_id'=>$this->integer(11)->notNull(),
            'user_id'=>$this->integer(11)->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);


//        $this->addForeignKey('sasq_survey_answer_survey_question', 'survey_answer', 'survey_question_id', 'survey_question', 'id');

        // Student
        $this->createIndex('idx-rel_student-student_id', 'military', 'student_id');
        $this->addForeignKey('fk-rel_student-student_id', 'military', 'student_id', 'student', 'id', 'CASCADE');
        // User
        $this->createIndex('idx-rel_user-user_id', 'military', 'user_id');
        $this->addForeignKey('fk-rel_user-user_id', 'military', 'user_id', 'users', 'id', 'CASCADE');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Edu_year
        $this->dropForeignKey('fk-rel_student-student_id', 'military');
        $this->dropIndex('idx-rel_student-student_id', 'military');
        // Edu_type
        $this->dropForeignKey('fk-rel_user-user_id', 'military');
        $this->dropIndex('idx-rel_user-user_id', 'military');


        $this->dropTable('{{%military}}');
    }
}
