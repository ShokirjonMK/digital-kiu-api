<?php

use yii\db\Migration;

/**
 * Class m211022_134710_student_new
 */
class m211022_134710_student_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student');
        }

        $this->createTable('student', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
            'is_contract' => $this->integer()->notNull(),
            'diplom_number' => $this->string(255)->Null(),
            'diplom_seria' => $this->string(255)->Null(),
            'diplom_date' => $this->date()->Null(),
            'description' => $this->text()->Null(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('us_student_user_id', 'student', 'user_id', 'users', 'id');
        $this->addForeignKey('fs_student_faculty_id', 'student', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('ds_student_direction_id', 'student', 'direction_id', 'direction', 'id');
        $this->addForeignKey('cs_student_course_id', 'student', 'course_id', 'course', 'id');
        $this->addForeignKey('es_student_edu_year_id', 'student', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('es_student_edu_type_id', 'student', 'edu_type_id', 'edu_type', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('us_student_user_id', 'student');
        $this->dropForeignKey('fs_student_faculty_id', 'student');
        $this->dropForeignKey('ds_student_direction_id', 'student');
        $this->dropForeignKey('cs_student_course_id', 'student');
        $this->dropForeignKey('es_student_edu_year_id', 'student');
        $this->dropForeignKey('es_student_edu_type_id', 'student');
        $this->dropTable('student');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211022_134710_student_new cannot be reverted.\n";

        return false;
    }
    */
}
