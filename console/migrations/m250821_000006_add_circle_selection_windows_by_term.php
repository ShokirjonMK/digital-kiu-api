<?php

use yii\db\Migration;

class m250821_000006_add_circle_selection_windows_by_term extends Migration
{
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'course';
        if ($this->db->getTableSchema($tableName, true) === null) {
            return true;
        }

        // Kuz (Fall) window: format mm-dd HH:ii:ss (no year)
        $this->addColumn('course', 'circle_kuz_from', $this->string(16)->null()->comment("Fall selection start (format mm-dd HH:ii:ss)"));
        $this->addColumn('course', 'circle_kuz_to', $this->string(16)->null()->comment("Fall selection end (format mm-dd HH:ii:ss)"));

        // Bahor (Spring) window: format mm-dd HH:ii:ss (no year)
        $this->addColumn('course', 'circle_bahor_from', $this->string(16)->null()->comment("Spring selection start (format mm-dd HH:ii:ss)"));
        $this->addColumn('course', 'circle_bahor_to', $this->string(16)->null()->comment("Spring selection end (format mm-dd HH:ii:ss)"));

        $this->createIndex('idx_course_circle_kuz_from', 'course', 'circle_kuz_from');
        $this->createIndex('idx_course_circle_kuz_to', 'course', 'circle_kuz_to');
        $this->createIndex('idx_course_circle_bahor_from', 'course', 'circle_bahor_from');
        $this->createIndex('idx_course_circle_bahor_to', 'course', 'circle_bahor_to');
    }

    public function safeDown()
    {
        if ($this->db->getTableSchema('course', true) === null) {
            return true;
        }
        $this->dropIndex('idx_course_circle_kuz_from', 'course');
        $this->dropIndex('idx_course_circle_kuz_to', 'course');
        $this->dropIndex('idx_course_circle_bahor_from', 'course');
        $this->dropIndex('idx_course_circle_bahor_to', 'course');
        $this->dropColumn('course', 'circle_kuz_from');
        $this->dropColumn('course', 'circle_kuz_to');
        $this->dropColumn('course', 'circle_bahor_from');
        $this->dropColumn('course', 'circle_bahor_to');
    }
}
