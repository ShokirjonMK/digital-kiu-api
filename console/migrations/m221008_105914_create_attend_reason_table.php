<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attend_reason}}`.
 */
class m221008_105914_create_attend_reason_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'attend_reason';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend_reason');
        }

        $this->createTable('{{%attend_reason}}', [
            'id' => $this->primaryKey(),
            'start' => $this->date()->notNull(),
            'end' => $this->date()->notNull(),
            'student_id' => $this->integer()->notNull(),

            'subject_id' => $this->integer()->null(),

            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'file' => $this->string()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);


        $this->addForeignKey('ars_mk_attend_reason_student_id', 'attend_reason', 'student_id', 'student', 'id');
        $this->addForeignKey('ars_mk_attend_reason_faculty_id', 'attend_reason', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('ars_mk_attend_reason_edu_plan_id', 'attend_reason', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('ars_mk_attend_reason_subject', 'attend_reason', 'subject_id', 'subject', 'id');



        $this->addForeignKey('amk_attend_time_table', 'attend', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('amk_attend_subject_category', 'attend', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('amk_attend_time_option', 'attend', 'time_option_id', 'time_option', 'id');
        $this->addForeignKey('amk_attend_edu_year', 'attend', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('amk_attend_edu_semestr', 'attend', 'edu_semestr_id', 'edu_semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ars_mk_attend_reason_student_id', 'attend_reason');
        $this->dropForeignKey('ars_mk_attend_reason_faculty_id', 'attend_reason');
        $this->dropForeignKey('ars_mk_attend_reason_edu_plan_id', 'attend_reason');
        $this->dropForeignKey('ars_mk_attend_reason_subject', 'attend_reason');

        $this->dropTable('{{%attend_reason}}');
    }
}
