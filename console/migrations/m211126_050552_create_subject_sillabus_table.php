<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_sillabus}}`.
 */
class m211126_050552_create_subject_sillabus_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subject_sillabus}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'subject_type_id' => $this->integer()->notNull(),
            'edu_semestr_exams_types' => $this->string()->notNull(),
            'edu_semestr_subject_category_times' => $this->string()->notNull(),

            'all_ball_yuklama' => $this->double()->Null(),
            'max_ball' => $this->double()->Null(),
            'credit' => $this->double()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('sss_subject_sillabus_subject', 'subject_sillabus', 'subject_id', 'subject', 'id');
        $this->addForeignKey('sss_subject_sillabus_subject_type', 'subject_sillabus', 'subject_type_id', 'subject_type', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eqe_subject_sillabus_subject', 'subject_sillabus');
        $this->dropForeignKey('sss_subject_sillabus_subject_type', 'subject_sillabus');

        $this->dropTable('{{%subject_sillabus}}');
    }
}
