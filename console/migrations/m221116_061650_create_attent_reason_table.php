<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attent_reason}}`.
 */
class m221116_061650_create_attent_reason_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");

        $tableName = Yii::$app->db->tablePrefix . 'attend_reason';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend_reason');
        }

        $this->createTable('{{%attend_reason}}', [
            'id' => $this->primaryKey(),
            'is_confirmed' => $this->tinyInteger(1)->defaultValue(0),
            'start' => $this->dateTime()->notNull(),
            'end' => $this->dateTime()->notNull(),
            'student_id' => $this->integer()->notNull(),

            'subject_id' => $this->integer()->null(),

            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'file' => $this->string()->null(),
            'description' => $this->text()->null(),

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
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
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
