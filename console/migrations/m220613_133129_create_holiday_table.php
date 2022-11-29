<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%holiday}}`.
 */
class m220613_133129_create_holiday_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'holiday';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('holiday');
        }

        $this->createTable('{{%holiday}}', [
            'id' => $this->primaryKey(),
            'start_date' => $this->date()->notNull(),
            'finish_date' => $this->date()->notNull(),
            'year' => $this->integer()->Null(),
            'month' => $this->integer()->Null(),

            // 'name' => $this->string(255)->null(),
            // 'description' => $this->text()->null(),

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
        $this->dropTable('{{%holiday}}');
    }
}
