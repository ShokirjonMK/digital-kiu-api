<?php

use yii\db\Migration;

/**
 * Class m211012_125050_edu_semestr_subject
 */
class m211012_125050_edu_semestr_subject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('edu_semestr_subject', [
            'id' => $this->primaryKey(),
            'edu_semestr_id'=>$this->integer()->notNull(),
            'subject_id'=>$this->integer()->notNull(),
            'subject_type_id'=>$this->integer()->notNull(),
            'credit'=>$this->float()->notNull(),
            'all_ball_yuklama'=>$this->integer()->notNull(),
            'is_checked'=>$this->integer()->notNull(),
            'max_ball'=>$this->integer()->notNull(),


            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);

        $this->addForeignKey('je_edu_semestr_subject_edu_semestr_id','edu_semestr_subject','edu_semestr_id','edu_semestr','id');
        $this->addForeignKey('se_edu_semestr_subject_subject_id','edu_semestr_subject','subject_id','subject','id');
        $this->addForeignKey('te_edu_semestr_subject_subject_type_id','edu_semestr_subject','subject_type_id','subject_type','id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('je_edu_semestr_subject_edu_semestr_id','edu_semestr_subject');
        $this->dropForeignKey('se_edu_semestr_subject_subject_id','edu_semestr_subject');
        $this->dropForeignKey('te_edu_semestr_subject_subject_type_id','edu_semestr_subject');
        $this->dropTable('edu_semestr_subject');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211012_125050_edu_semestr_subject cannot be reverted.\n";

        return false;
    }
    */
}
