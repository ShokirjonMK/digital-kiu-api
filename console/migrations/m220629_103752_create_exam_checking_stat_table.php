<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_checking_stat}}`.
 */
class m220629_103752_create_exam_checking_stat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%exam_checking_stat}}', [
            'id' => $this->primaryKey(),

            'data' => $this->json()->null()->comment('asosiy data json qilib yoziladi'),

            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_checking_stat}}');
    }
}
