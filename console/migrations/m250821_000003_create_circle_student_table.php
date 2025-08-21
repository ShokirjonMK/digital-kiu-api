<?php

use yii\db\Migration;

class m250821_000003_create_circle_student_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'circle_student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('circle_student');
        }

        $this->createTable('circle_student', [
            'id' => $this->primaryKey(),
            'circle_id' => $this->integer()->notNull()->comment('circle id'),  // circle_schedule->circle_id ni yozish kerak
            'circle_schedule_id' => $this->integer()->notNull()->comment('circle schedule id'),
            'student_user_id' => $this->integer()->notNull()->comment('student user id'),
            'student_id' => $this->integer()->notNull()->comment('student id'),  // student->id ni yozish kerak
            'is_finished' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('1 tamomladi 0 tamomlamadi'),  // circle_schedule->end_date < today() dan keyin yozish mumkin. circle_schedule->teacher_user_id == Current_user_id() yoki admin yozish mumkin.
            'abs_status' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('1 abs 0 abs emas'),  // circle_schedule->abs_count circle_attendance(reason == 1, circle_attendance.student_id == circle_schedule.student_id, circle_attendance.circle_id == circle_schedule.circle_id) count dan katta bo'lsa 0 qo'yiladi, kichik bo'lsa 1 qo'yiladi. circle_schedule->end_date < today() dan keyin yozish mumkin.
            'certificate_status' => $this->tinyInteger(1)->notNull()->defaultValue(0)->comment('1 tamomladi 0 tamomlamadi'),  //  circle_schedule->end_date < today() dan keyin yozish mumkin
            'certificate_file' => $this->string(255)->null()->comment('sertifikat fayli'),  // circle_schedule->end_date < today() dan keyin sertifikat olish uchun student so'rov yuboradi va (certificate_status = 1 bo'lsa yoki (abs_status = 1 va is_finished = 1)) bo'lsa sertifikat fayl shakllantiriladi pdf generatisya qilinadi.
            'certificate_date' => $this->integer()->null()->comment('sertifikat sanasi'),  // sertifikat fayli generatsiya qilingan sanasi. circle_schedule->end_date < today() dan keyin yozish mumkin
            'status' => $this->tinyInteger(1)->notNull()->defaultValue(1)->comment('1 aktiv 0 deaktiv'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment("1 o'chirilgan 0 aktiv"),
            'created_at' => $this->integer()->notNull()->comment('yaratilgan vaqt'),
            'updated_at' => $this->integer()->notNull()->comment('yangilangan vaqt'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('yaratilgan foydalanuvchi id'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('yangilangan foydalanuvchi id'),
        ], $tableOptions);

        // circle_id, student_user_id, is_deleted UNIQUE qo'yiladi
        $this->createIndex('idx_unique_circle_student', 'circle_student', ['circle_id', 'student_user_id', 'is_deleted'], true);

        $this->addForeignKey('fk_circle_student_circle', 'circle_student', 'circle_id', 'circle', 'id');
        $this->addForeignKey('fk_circle_student_schedule', 'circle_student', 'circle_schedule_id', 'circle_schedule', 'id');
        $this->addForeignKey('fk_circle_student_student', 'circle_student', 'student_user_id', 'users', 'id');
        $this->createIndex('idx_unique_enroll', 'circle_student', ['circle_schedule_id', 'student_user_id', 'is_deleted'], true);
        $this->createIndex('idx_circle_student_circle_schedule_id', 'circle_student', 'circle_schedule_id');
        $this->createIndex('idx_circle_student_student_user_id', 'circle_student', 'student_user_id');
        $this->createIndex('idx_circle_student_student_number', 'circle_student', 'student_number');
        $this->createIndex('idx_circle_student_status', 'circle_student', 'status');
        $this->createIndex('idx_circle_student_is_deleted', 'circle_student', 'is_deleted');
        $this->createIndex('idx_circle_student_created_at', 'circle_student', 'created_at');
        $this->createIndex('idx_circle_student_updated_at', 'circle_student', 'updated_at');
        $this->createIndex('idx_circle_student_created_by', 'circle_student', 'created_by');
        $this->createIndex('idx_circle_student_updated_by', 'circle_student', 'updated_by');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_circle_student_schedule', 'circle_student');
        $this->dropForeignKey('fk_circle_student_student', 'circle_student');
        $this->dropIndex('idx_circle_student_circle_schedule_id', 'circle_student');
        $this->dropIndex('idx_circle_student_student_user_id', 'circle_student');
        $this->dropIndex('idx_circle_student_student_number', 'circle_student');
        $this->dropIndex('idx_circle_student_status', 'circle_student');
        $this->dropIndex('idx_circle_student_is_deleted', 'circle_student');
        $this->dropIndex('idx_circle_student_created_at', 'circle_student');
        $this->dropIndex('idx_circle_student_updated_at', 'circle_student');
        $this->dropIndex('idx_circle_student_created_by', 'circle_student');
        $this->dropIndex('idx_circle_student_updated_by', 'circle_student');
        $this->dropTable('circle_student');
    }
}
