<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_category}}`.
 */
class m220607_055958_create_kpi_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'kpi_category';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_category');
        }

        $this->createTable('{{%kpi_category}}', [
            'id' => $this->primaryKey(),

            'fields' => $this->string(255)->Null()->comment('["date", "file", "subject_category", "count_of_copyright", "link"]'),
            'max_ball' => $this->double()->defaultValue(0),
            'term' => $this->tinyInteger(1)->defaultValue(1)->comment('muddati 1-bir yil 2-olti oy'),
            'tab' => $this->tinyInteger(1)->defaultValue(1)->comment('tab raqami'),

            'status' => $this->tinyInteger(1)->defaultValue(0),
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
        $this->dropTable('{{%kpi_category}}');
    }
}
