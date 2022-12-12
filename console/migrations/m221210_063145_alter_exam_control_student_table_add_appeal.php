<?php

use yii\db\Migration;

/**
 * Class m221210_063145_alter_exam_control_student_table_add_appeal
 */
class m221210_063145_alter_exam_control_student_table_add_appeal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_control_student', 'appeal_text', $this->text()->null()->after('status'));
        $this->addColumn('exam_control_student', 'appeal', $this->integer()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221210_063145_alter_exam_control_student_table_add_appeal cannot be reverted.\n";

        return false;
    }
}
