<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_marking}}`.
 */
class m220818_133042_create_kpi_marking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'kpi_marking';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_marking');
        }

        $this->createTable('{{%kpi_marking}}', [
            'id' => $this->primaryKey(),
            'kpi_category_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'ball'=>$this->double()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('kpi_marking_kpi_user_id', 'kpi_marking', 'user_id', 'users', 'id');
        $this->addForeignKey('kpi_marking_kpi_category_id', 'kpi_marking', 'kpi_category_id', 'kpi_category', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('kpi_marking_user_id', 'kpi_marking');
        $this->dropForeignKey('kpi_marking_kpi_category_id', 'kpi_marking');


        $this->dropTable('{{%kpi_marking}}');
    }
}
