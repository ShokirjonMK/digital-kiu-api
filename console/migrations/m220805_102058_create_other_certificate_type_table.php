<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%other_certificate_type}}`.
 */
class m220805_102058_create_other_certificate_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'other_certificate_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('other_certificate_type');
        }

        $this->createTable('{{%other_certificate_type}}', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(255)->null(),
            'lang' => $this->string(2)->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%other_certificate_type}}');
    }
}
