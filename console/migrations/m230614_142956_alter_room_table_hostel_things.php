<?php

use yii\db\Migration;

/**
 * Class m230614_142956_alter_room_table_hostel_things
 */
class m230614_142956_alter_room_table_hostel_things extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->tablePrefix . 'room';
        $schema = Yii::$app->db->getTableSchema($table, true);

        if ($schema !== null && !isset($schema->columns['price'])) {
            $this->addColumn('room', 'price', $this->double()->null()->after('id')->comment('room price'));
        }
        if ($schema !== null && !isset($schema->columns['empty_count'])) {
            $this->addColumn('room', 'empty_count', $this->integer()->null()->after('id')->comment('bosh joylar soni'));
        }
        if ($schema !== null && !isset($schema->columns['gender'])) {
            $this->addColumn('room', 'gender', $this->integer()->defaultValue(1)->after('id')->comment('room gender male 1 female 0'));
        }
        if ($schema !== null && !isset($schema->columns['type'])) {
            $this->addColumn('room', 'type', $this->integer()->defaultValue(1)->after('id')->comment('type education building or hostel or something'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_142956_alter_room_table_hostel_things cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_142956_alter_room_table_hostel_things cannot be reverted.\n";

        return false;
    }
    */
}
