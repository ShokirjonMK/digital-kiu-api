<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_order}}`.
 */
class m220805_102226_create_student_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'student_order';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_order');
        }

        $this->createTable('{{%student_order}}', [
            'id' => $this->primaryKey(),

            'order_type_id' => $this->integer()->notNull(),
            'date' => $this->string(11)->notNull(),
            'file' => $this->string(255)->notNull(),
            'student_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ]);
        // Student
        $this->addForeignKey('sos_student_order_student_id', 'student_order', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('sou_student_order_user_id', 'student_order', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // student
        $this->dropForeignKey('sos_student_order_student_id', 'sport_certificate');

        // user
        $this->dropForeignKey('sou_student_order_user_id', 'sport_certificate');


        $this->dropTable('{{%student_order}}');
    }
}
