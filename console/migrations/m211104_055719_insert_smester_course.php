<?php

use yii\db\Migration;

/**
 * Class m211104_055719_insert_smester_course
 */
class m211104_055719_insert_smester_course extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Yii::$app->db->createCommand()->truncateTable('course')->execute();

        Yii::$app->db->createCommand()->delete('course', ['id' => 1])->execute();
        $this->insert('{{%course}}', [
            'id' => 1,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 1, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 1,
            'name' => '1',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '1',
        ]);

        Yii::$app->db->createCommand()->delete('course', ['id' => 2])->execute();
        $this->insert('{{%course}}', [
            'id' => 2,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 2, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 2,
            'name' => '2',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '2',
        ]);

        Yii::$app->db->createCommand()->delete('course', ['id' => 3])->execute();
        $this->insert('{{%course}}', [
            'id' => 3,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 3, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 3,
            'name' => '3',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '3',
        ]);

        Yii::$app->db->createCommand()->delete('course', ['id' => 4])->execute();
        $this->insert('{{%course}}', [
            'id' => 4,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 4, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 4,
            'name' => '4',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '4',
        ]);

        Yii::$app->db->createCommand()->delete('course', ['id' => 5])->execute();
        $this->insert('{{%course}}', [
            'id' => 5,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 5, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 5,
            'name' => '5',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '5',
        ]);

        Yii::$app->db->createCommand()->delete('course', ['id' => 6])->execute();
        $this->insert('{{%course}}', [
            'id' => 6,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 6, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 6,
            'name' => '6',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '6',
        ]);


        // Yii::$app->db->createCommand()->truncateTable('semestr')->execute();

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 1])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 1,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 1, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 1,
            'name' => '1',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '1',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 2])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 2,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 2, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 2,
            'name' => '2',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '2',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 3])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 3,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 3, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 3,
            'name' => '3',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '3',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 4])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 4,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 4, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 4,
            'name' => '4',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '4',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 5])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 5,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 5, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 5,
            'name' => '5',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '5',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 6])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 6,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 6, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 6,
            'name' => '6',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '6',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 7])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 7,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 7, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 7,
            'name' => '7',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '7',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 8])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 8,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 8, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 8,
            'name' => '8',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '8',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 9])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 9,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 9, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 9,
            'name' => '9',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '9',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 10])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 10,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 10, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 10,
            'name' => '10',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '10',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 11])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 11,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 11, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 11,
            'name' => '11',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '11',
        ]);

        Yii::$app->db->createCommand()->delete('semestr', ['id' => 12])->execute();
        $this->insert('{{%semestr}}', [
            'id' => 12,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 12, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 12,
            'name' => '12',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '12',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211104_055719_insert_smester_course cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211104_055719_insert_smester_course cannot be reverted.\n";

        return false;
    }
    */
}
