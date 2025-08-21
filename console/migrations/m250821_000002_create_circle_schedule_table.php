<?php

use yii\db\Migration;

class m250821_000002_create_circle_schedule_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'circle_schedule';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('circle_schedule');
        }

        $this->createTable('circle_schedule', [
            'id' => $this->primaryKey(),
            'circle_id' => $this->integer()->notNull()->comment('circle id'),
            'building_id' => $this->integer()->notNull()->comment('building id'),
            'room_id' => $this->integer()->null()->comment('room id'),
            'start_date' => $this->date()->null()->comment('boshlash sanasi'),
            'end_date' => $this->date()->null()->comment('tugash sana'),
            'start_time' => $this->string(10)->notNull()->comment('boshlanish vaqti: 10:00'),
            'end_time' => $this->string(10)->notNull()->comment('tugash vaqti: 12:00'),
            'week_id' => $this->integer()->notNull()->comment('hafta id'),
            'abs_count' => $this->integer()->null()->defaultValue(4)->comment('nb lar soni'),
            'max_student_count' => $this->integer()->defaultValue(30)->notNull()->comment('maksimal talaba soni'),  // 30 ta talaba qo'shiladi
            'student_count' => $this->integer()->notNull()->defaultValue(0)->comment('talaba soni'),  // circle_student count update qilinadi
            'teacher_user_id' => $this->integer()->notNull()->comment('teacher id'),
            'edu_year_id' => $this->integer()->notNull()->comment('edu year id'),
            'semestr_type' => $this->tinyInteger(1)->defaultValue(1)->comment('1 kuz 2 bahor'),  // eduYear->type yoziladi
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('1 aktiv 0 deaktiv'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment("1 o'chirilgan 0 aktiv"),
            'created_at' => $this->integer()->notNull()->comment('yaratilgan vaqt'),
            'updated_at' => $this->integer()->notNull()->comment('yangilangan vaqt'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('yaratilgan foydalanuvchi id'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('yangilangan foydalanuvchi id'),
        ], $tableOptions);

        $this->addForeignKey('fk_circle_schedule_circle', 'circle_schedule', 'circle_id', 'circle', 'id');
        $this->addForeignKey('fk_circle_schedule_week', 'circle_schedule', 'week_id', 'week', 'id');
        $this->addForeignKey('fk_circle_schedule_teacher', 'circle_schedule', 'teacher_user_id', 'users', 'id');
        $this->addForeignKey('fk_circle_schedule_edu_year', 'circle_schedule', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('fk_circle_schedule_building', 'circle_schedule', 'building_id', 'building', 'id');
        $this->addForeignKey('fk_circle_schedule_room', 'circle_schedule', 'room_id', 'room', 'id');

        $this->createIndex('idx_circle_schedule_circle_id', 'circle_schedule', 'circle_id');
        $this->createIndex('idx_circle_schedule_week_id', 'circle_schedule', 'week_id');
        $this->createIndex('idx_circle_schedule_teacher_user_id', 'circle_schedule', 'teacher_user_id');
        $this->createIndex('idx_circle_schedule_edu_year_id', 'circle_schedule', 'edu_year_id');
        $this->createIndex('idx_circle_schedule_status', 'circle_schedule', 'status');
        $this->createIndex('idx_circle_schedule_is_deleted', 'circle_schedule', 'is_deleted');
        $this->createIndex('idx_circle_schedule_created_at', 'circle_schedule', 'created_at');
        $this->createIndex('idx_circle_schedule_updated_at', 'circle_schedule', 'updated_at');
        $this->createIndex('idx_circle_schedule_created_by', 'circle_schedule', 'created_by');
        $this->createIndex('idx_circle_schedule_updated_by', 'circle_schedule', 'updated_by');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_circle_schedule_circle', 'circle_schedule');
        $this->dropForeignKey('fk_circle_schedule_week', 'circle_schedule');
        $this->dropForeignKey('fk_circle_schedule_teacher', 'circle_schedule');
        $this->dropForeignKey('fk_circle_schedule_edu_year', 'circle_schedule');
        $this->dropForeignKey('fk_circle_schedule_building', 'circle_schedule');
        $this->dropForeignKey('fk_circle_schedule_room', 'circle_schedule');

        $this->dropIndex('idx_circle_schedule_circle_id', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_room_id', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_week_id', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_teacher_user_id', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_edu_year_id', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_status', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_is_deleted', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_created_at', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_updated_at', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_created_by', 'circle_schedule');
        $this->dropIndex('idx_circle_schedule_updated_by', 'circle_schedule');
        $this->dropTable('circle_schedule');
    }
}
