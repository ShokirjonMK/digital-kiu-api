<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_mark}}`.
 */
class m221227_044617_create_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_mark';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_mark');
        }

        $this->createTable('{{%student_mark}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer(11)->notNull(),



            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('sto_student_order_student_id', 'student_order', 'student_id', 'student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sto_student_order_student_id', 'student_order');

        $this->dropTable('{{%student_mark}}');
    }
}
