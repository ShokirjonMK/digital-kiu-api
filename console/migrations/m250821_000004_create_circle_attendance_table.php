<?php

use yii\db\Migration;

class m250821_000004_create_circle_attendance_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'circle_attendance';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('circle_attendance');
        }

        $this->createTable('circle_attendance', [
            'id' => $this->primaryKey(),
            'circle_student_id' => $this->integer()->notNull()->comment('circle student id'),  // circle_student->id ni yozish kerak
            'circle_id' => $this->integer()->null()->comment('circle id'),  // circle_student->circle_id ni yozish kerak
            'circle_schedule_id' => $this->integer()->notNull()->comment('circle schedule id'),  // circle_student->circle_schedule_id ni yozish kerak
            'student_id' => $this->integer()->notNull()->comment('student id'),  // circle_student->student_id ni yozish kerak
            'teacher_user_id' => $this->integer()->null()->comment('teacher user id'),  // circle_student->teacher_user_id ni yozish kerak
            'date' => $this->date()->notNull()->comment('attendance date'),  // circle_schedule->start_date va circle_schedule->end_date orasida yozish kerak
            'reason' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('attendance reason'),  // sababli qilish yoki qilmaslik 1 sababli qilish 0 sababli qilmaslik admin yoki circle_schedule->teacher_user_id yozishi mumkin
            'reason_text' => $this->string()->null()->comment('attendance reason text'),  // sababli qilish yoki qilmaslik 1 sababli qilish 0 sababli qilmaslik admin yoki circle_schedule->teacher_user_id yozishi mumkin
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1 aktiv 0 deaktiv'),
            'is_deleted' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('1 o\'chirilgan 0 aktiv'),
            'created_at' => $this->integer()->notNull()->comment('yaratilgan vaqt'),
            'updated_at' => $this->integer()->notNull()->comment('yangilangan vaqt'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('yaratilgan foydalanuvchi id'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('yangilangan foydalanuvchi id'),
        ], $tableOptions);

        $this->addForeignKey('fk_circle_attendance_circle_student', 'circle_attendance', 'circle_student_id', 'circle_student', 'id');
        $this->addForeignKey('fk_circle_attendance_circle', 'circle_attendance', 'circle_id', 'circle', 'id');
        $this->addForeignKey('fk_circle_attendance_schedule', 'circle_attendance', 'circle_schedule_id', 'circle_schedule', 'id');
        $this->addForeignKey('fk_circle_attendance_student', 'circle_attendance', 'student_id', 'users', 'id');
        $this->addForeignKey('fk_circle_attendance_teacher', 'circle_attendance', 'teacher_user_id', 'users', 'id');

        $this->createIndex('idx_circle_attendance_circle_id', 'circle_attendance', 'circle_id');
        $this->createIndex('idx_circle_attendance_schedule_id', 'circle_attendance', 'circle_schedule_id');
        $this->createIndex('idx_circle_attendance_student_id', 'circle_attendance', 'student_id');
        $this->createIndex('idx_circle_attendance_teacher_user_id', 'circle_attendance', 'teacher_user_id');
        $this->createIndex('idx_circle_attendance_status', 'circle_attendance', 'status');
        $this->createIndex('idx_circle_attendance_created_at', 'circle_attendance', 'created_at');
        $this->createIndex('idx_circle_attendance_updated_at', 'circle_attendance', 'updated_at');
        $this->createIndex('idx_circle_attendance_created_by', 'circle_attendance', 'created_by');
        $this->createIndex('idx_circle_attendance_updated_by', 'circle_attendance', 'updated_by');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_circle_attendance_circle_student', 'circle_attendance');
        $this->dropForeignKey('fk_circle_attendance_circle', 'circle_attendance');
        $this->dropForeignKey('fk_circle_attendance_schedule', 'circle_attendance');
        $this->dropForeignKey('fk_circle_attendance_student', 'circle_attendance');
        $this->dropForeignKey('fk_circle_attendance_teacher', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_circle_id', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_schedule_id', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_student_id', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_teacher_user_id', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_status', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_created_at', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_updated_at', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_created_by', 'circle_attendance');
        $this->dropIndex('idx_circle_attendance_updated_by', 'circle_attendance');
        $this->dropTable('circle_attendance');
    }
}
