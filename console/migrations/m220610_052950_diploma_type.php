<?php

use yii\db\Migration;

/**
 * Class m220610_052950_diploma_type
 */
class m220610_052950_diploma_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS `diploma_type`;
            CREATE TABLE `diploma_type`  (
              `id` int(0) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
              `name_ru` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
              `name_en` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
              `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
              `status` int(0) NULL DEFAULT NULL,
              `created_at` timestamp(0) NULL DEFAULT NULL,
              `updated_at` timestamp(0) NULL DEFAULT NULL,
              `updated_at` timestamp(0) NULL DEFAULT NULL,
              `updated_by` timestamp(0) NULL DEFAULT NULL,
              PRIMARY KEY (`id`) USING BTREE
            ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220610_052950_diploma_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_052950_diploma_type cannot be reverted.\n";

        return false;
    }
    */
}
