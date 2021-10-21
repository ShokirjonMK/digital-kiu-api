<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area}}`.
 */
class m211021_122652_area_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents(__DIR__ . '/../sql/area.sql');
        \Yii::$app->db->pdo->exec($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%area}}');
    }
}
