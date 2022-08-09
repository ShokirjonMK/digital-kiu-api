<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sport_certificate}}`.
 */
class m220805_101919_create_sport_certificate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'sport_certificate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('sport_certificate');
        }

        $this->createTable('{{%sport_certificate}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(11)->Null(),
            'date' => $this->date()->notNull(),
            'year' => $this->string(11)->Null(),
            'address' => $this->string(255)->Null(),
            'file' => $this->string(255)->notNull(),
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
        $this->addForeignKey('sport_certificate-student_id', 'sport_certificate', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('sport_certificate-user_id', 'sport_certificate', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // student
        $this->dropForeignKey('sport_certificate-student_id', 'sport_certificate');

        // user
        $this->dropForeignKey('sport_certificate-user_id', 'sport_certificate');


        $this->dropTable('{{%sport_certificate}}');
    }
}
