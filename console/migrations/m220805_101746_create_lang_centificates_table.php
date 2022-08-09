<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lang_centificates}}`.
 */
class m220805_101746_create_lang_centificates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'lang_centificates';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('lang_centificates');
        }

        $this->createTable('{{%lang_centificates}}', [
            'id' => $this->primaryKey(),
            'certificate_type_id' => $this->integer(11)->notNull(),
            'ball' => $this->string(11)->notNull(),
            'file' => $this->string()->notNull(),
            'lang' => $this->string(2)->notNull(),
            'student_id' => $this->integer()->notNull(),
            'user_id'=>$this->integer()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ]);
        // Student
        $this->addForeignKey('lang_centificates-student_id', 'lang_centificates', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('lang_centificates-user_id', 'lang_centificates', 'user_id', 'users', 'id');

        // Certificate_type
        $this->addForeignKey('lang_centificates-certificate_type_id', 'lang_centificates', 'certificate_type_id', 'lang_certificate_type', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // student
        $this->dropForeignKey('lang_centificates-student_id', 'lang_centificates');
        // user
        $this->dropForeignKey('lang_centificates-user_id', 'lang_centificates');
//         Certificate_type
        $this->dropForeignKey('lang_centificates-certificate_type_id', 'lang_centificates');


        $this->dropTable('{{%lang_centificates}}');
    }
}
