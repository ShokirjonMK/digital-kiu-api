<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%parent_info}}`.
 */
class m220805_102145_create_parent_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'parent_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('parent_info');
        }

        $this->createTable('{{%parent_info}}', [
            'id' => $this->primaryKey(),
            'last_name' => $this->string(255)->null(),
            'first_name' => $this->string(255)->null(),
            'middle_name' => $this->string(255)->null(),
            'type' => $this->integer(11)->null(),
            'phone' => $this->string(55)->null(),
            'description' => $this->text()->null(),
            'student_id'=>$this->integer(11)->notNull(),
            'user_id'=>$this->integer(11)->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);
        // Student
        $this->addForeignKey('parent_info-student_id', 'parent_info', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('parent_info-user_id', 'parent_info', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // student
        $this->dropForeignKey('parent_info-student_id', 'sport_certificate');

        // user
        $this->dropForeignKey('parent_info-user_id', 'sport_certificate');



        $this->dropTable('{{%parent_info}}');
    }
}
