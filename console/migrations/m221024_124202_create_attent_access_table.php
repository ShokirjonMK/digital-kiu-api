<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attent_access}}`.
 */
class m221024_124202_create_attent_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'attent_access';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attent_access');
        }
        $this->createTable('{{%attent_access}}', [
            'id' => $this->primaryKey(),

            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'time_table_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'time_option_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),

            'type' => $this->tinyInteger(1)->defaultValue(1)->comment('1 kuz 2 bohor'),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('mk_a_a_attent_access_time_table_id', 'attent_access', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('mk_a_a_attent_access_subject_id', 'attent_access', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_a_a_attent_access_user_id', 'attent_access', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_a_a_attent_access_edu_year_id', 'attent_access', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_a_a_attent_access_subject_category_id', 'attent_access', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_a_a_attent_access_time_option_id', 'attent_access', 'time_option_id', 'time_option', 'id');
        $this->addForeignKey('mk_a_a_attent_access_faculty_id', 'attent_access', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_a_a_attent_access_edu_plan_id', 'attent_access', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_a_a_attent_access_edu_semestr_id', 'attent_access', 'edu_semestr_id', 'edu_semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_a_a_attent_access_time_table_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_subject_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_user_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_edu_year_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_subject_category_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_time_option_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_faculty_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_edu_plan_id', 'attent_access');
        $this->dropForeignKey('mk_a_a_attent_access_edu_semestr_id', 'attent_access');

        $this->dropTable('{{%attent_access}}');
    }
}
