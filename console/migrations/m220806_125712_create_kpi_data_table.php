<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_data}}`.
 */
class m220806_125712_create_kpi_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'kpi_data';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_data');
        }

        $this->createTable('{{%kpi_data}}', [
            'id' => $this->primaryKey(),
            'kpi_category_id' => $this->integer()->notNull(),
            'date' => $this->date()->Null(),
            'file' => $this->string(255)->null(),
            'file2' => $this->string(255)->null(),
            'file3' => $this->string(255)->null(),
            'start_date' => $this->date()->Null(),
            'end_date' => $this->date()->Null(),
            'link' => $this->string(255)->null(),
            'link2' => $this->string(255)->null(),
            'ball' => $this->double()->null(),
            'count' => $this->integer()->Null(),
            'subject_category_id' => $this->integer()->Null(),
            'event_type' => $this->integer()->Null(),
            'event_form' => $this->integer()->Null(),

            'number' => $this->string(255)->null(),



count
level
name
name1
name2
name3
authors


            'count_of_copyright' => $this->integer()->defaultValue(0),



            'user_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ]);

        $this->addForeignKey('kpiskc_kpi_data_kpi_category', 'kpi_store', 'kpi_category_id', 'kpi_category', 'id');
        $this->addForeignKey('kpissc_kpi_data_subject_category', 'kpi_store', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('kpissc_kpi_data_user', 'kpi_store', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('kpiskc_kpi_data_kpi_category', 'kpi_store');
        $this->dropForeignKey('kpissc_kpi_data_subject_category', 'kpi_store');
        $this->dropForeignKey('kpissc_kpi_data_user', 'kpi_store');

        $this->dropTable('{{%kpi_data}}');
    }
}
