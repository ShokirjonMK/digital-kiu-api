<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_reaxam}}`.
 */
class m230531_101758_create_exam_student_reaxam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_student_reaxam';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_reaxam');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%exam_student_reaxam}}', [
            'id' => $this->primaryKey(),

            'file' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'student_id' => $this->integer(11)->notNull(),
            'exam_student_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->notNull(),
            'exam_id' => $this->integer(11)->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('exam_student_reaxam_student_id', 'exam_student_reaxam', 'student_id', 'student', 'id');
        $this->addForeignKey('exam_student_reaxam_exam_student_id', 'exam_student_reaxam', 'exam_student_id', 'exam_student', 'id');
        $this->addForeignKey('exam_student_reaxam_subject_id', 'exam_student_reaxam', 'subject_id', 'subject', 'id');
        $this->addForeignKey('exam_student_reaxam_exam_id', 'exam_student_reaxam', 'exam_id', 'exam', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('exam_student_reaxam_student_id', 'exam_student_reaxam');
        $this->dropForeignKey('exam_student_reaxam_exam_student_id', 'exam_student_reaxam');
        $this->dropForeignKey('exam_student_reaxam_subject_id', 'exam_student_reaxam');
        $this->dropForeignKey('exam_student_reaxam_exam_id', 'exam_student_reaxam');
        $this->dropTable('{{%exam_student_reaxam}}');
    }
}
