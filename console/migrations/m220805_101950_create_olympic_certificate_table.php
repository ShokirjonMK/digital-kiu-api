<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%olympic_certificate}}`.
 */
class m220805_101950_create_olympic_certificate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'olympic_certificate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('olympic_certificate');
        }

        $this->createTable('{{%olympic_certificate}}', [
            'id' => $this->primaryKey(),

            'type' => $this->integer(11)->Null(),
            'address' => $this->string(255)->notNull(),
            'year' => $this->string(11)->notNull(),
            'file' => $this->string(255),
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
        $this->addForeignKey('olympic_certificate-student_id', 'olympic_certificate', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('olympic_certificate-user_id', 'olympic_certificate', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // student
        $this->dropForeignKey('olympic_certificate-student_id', 'sport_certificate');

        // user
        $this->dropForeignKey('olympic_certificate-user_id', 'sport_certificate');



        $this->dropTable('{{%olympic_certificate}}');
    }
}
