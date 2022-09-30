<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%club}}`.
 */
class m220927_045715_create_club_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'club';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('club');
        }

        $this->createTable('{{%club}}', [
            'id' => $this->primaryKey(),

            'club_category_id' => $this->integer()->notNull(),
            //names in translate
            //des in translate

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('ccc_club_club_category_id', 'club', 'club_category_id', 'club_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ccc_club_club_category_id', 'club');

        $this->dropTable('{{%club}}');
    }
}
