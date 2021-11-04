<?php

use yii\db\Migration;

/**
 * Class m211012_121546_semestr
 */
class m211012_121546_semestr extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('semestr', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);


        $this->execute("INSERT INTO `semestr` VALUES (1, 1, 1, 1635949968, 1635950073, 1, 1, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (2, 1, 1, 1635950765, 1635950765, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (3, 1, 1, 1635950771, 1635950771, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (4, 1, 1, 1635950777, 1635950777, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (5, 1, 1, 1635950784, 1635950784, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (6, 1, 1, 1635950789, 1635950789, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (7, 1, 1, 1635950795, 1635950795, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (8, 1, 1, 1635950800, 1635950800, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (9, 1, 1, 1635950805, 1635950805, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (10, 1, 1, 1635950817, 1635950817, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (11, 1, 1, 1635950822, 1635950822, 1, 0, 0);");
        $this->execute("INSERT INTO `semestr` VALUES (12, 1, 1, 1635950827, 1635950827, 1, 0, 0);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('semestr');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_121546_semestr cannot be reverted.\n";

        return false;
    }
    */
}
