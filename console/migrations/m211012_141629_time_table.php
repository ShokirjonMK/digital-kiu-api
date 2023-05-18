<?php

use yii\db\Migration;

/**
 * Class m211012_141629_time_table
 */
class m211012_141629_time_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('time_table', [
            'id' => $this->primaryKey(),
            'teacher_access_id' => $this->integer()->notNull(),
            'room_id' => $this->integer()->notNull(),
            'para_id' => $this->integer()->notNull(),
            'course_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('tt_time_table_teacher_access_id', 'time_table', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('rt_time_table_room_id', 'time_table', 'room_id', 'room', 'id');
        $this->addForeignKey('pt_time_table_para_id', 'time_table', 'para_id', 'para', 'id');
        $this->addForeignKey('ct_time_table_course_id', 'time_table', 'course_id', 'course', 'id');
        $this->addForeignKey('st_time_table_semestr_id', 'time_table', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('et_time_table_edu_year_id', 'time_table', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('st_time_table_edu_subject_id', 'time_table', 'subject_id', 'subject', 'id');
        $this->addForeignKey('lt_time_table_edu_language_id', 'time_table', 'language_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('tt_time_table_teacher_access_id', 'time_table');
        $this->dropForeignKey('rt_time_table_room_id', 'time_table');
        $this->dropForeignKey('pt_time_table_para_id', 'time_table');
        $this->dropForeignKey('ct_time_table_course_id', 'time_table');
        $this->dropForeignKey('st_time_table_semestr_id', 'time_table');
        $this->dropForeignKey('et_time_table_edu_year_id', 'time_table');
        $this->dropForeignKey('st_time_table_edu_subject_id', 'time_table');
        $this->dropForeignKey('lt_time_table_edu_language_id', 'time_table');
        $this->dropTable('time_table');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_141629_time_table cannot be reverted.\n";

        return false;
    }
    */
}
