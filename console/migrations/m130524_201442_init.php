<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'access_token' => $this->string(100)->defaultValue(null),
            'access_token_time' => $this->integer()->null(),
            'email' => $this->string()->notNull()->unique(),
            'template' => $this->string(255)->null(),
            'layout' => $this->string(255)->null(),
            'view' => $this->string(255)->null(),
            'meta' => $this->json(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'cacheable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'searchable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'is_changed' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('{{%profile}}', [

            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255)->null(),
            'middle_name' => $this->string(255)->null(),
            'image' => $this->string(255)->null(),
            'dob' => $this->timestamp()->null(),
            'birthday' => $this->timestamp()->null(),
            'gender' => $this->tinyInteger(1)->null(),
            'phone' => $this->string(50)->null(),
            'phone_secondary' => $this->string(50)->null(),

            'is_stateless' => $this->tinyInteger(1)->null(),
            'is_foreign' => $this->tinyInteger(1)->null(),
            'country_id' => $this->integer()->null(),
            'nationality_id' => $this->integer()->null(),
            'birth_place_id' => $this->integer()->null(),
            'permanent_place_id' => $this->integer()->null(),
            'permanent_address' => $this->string()->null(),
            'temporary_place_id' => $this->integer()->null(),
            'temporary_address' => $this->string()->null(),

            'has_disability' => $this->integer()->notNull()->defaultValue(0)->comment("nogirognligi bormi"),

            'passport_serial' => $this->string()->null(),
            'passport_number' => $this->string()->null(),
            'passport_pinip' => $this->string(14)->null(),
            'passport_given_place' => $this->string()->null(),
            'passport_given_date' => $this->timestamp()->null(),
            'passport_validity_date' => $this->timestamp()->null(),

            'residence_permit' => $this->tinyInteger(1)->null(),
            'residence_permit_no' => $this->string()->null(),
            'residence_permit_date' => $this->timestamp()->null(),
            'residence_permit_expire' => $this->timestamp()->null(),

        ], $tableOptions);

        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'department_id' => $this->integer()->null(),
            'job_id' => $this->integer()->null(),

            'inps' => $this->string()->null(),
            'scientific_work' => $this->text()->null(),
            'languages' => $this->string()->null(),
            'lang_certs' => $this->string()->null(),
            'rate' => $this->decimal(10, 2)->null(),
            'rank_id' => $this->integer()->null(),
            'science_degree_id' => $this->integer()->null(),
            'scientific_title_id' => $this->integer()->null(),
            'special_title_id' => $this->integer()->null(),
            'reception_time' => $this->string()->null(),
            'out_staff' => $this->tinyInteger(1)->null(),
            'basic_job' => $this->tinyInteger(1)->null(),

            'is_convicted' => $this->tinyInteger(1)->null(),
            'party_membership' => $this->tinyInteger(1)->null(),
            'awords' => $this->string()->null(),
            'depuities' => $this->string()->null(),
            'military_rank' => $this->string()->null(),
            'disability_group' => $this->tinyInteger(1)->null(),
            'family_status' => $this->tinyInteger(1)->null(),
            'children' => $this->string()->null(),
            'other_info' => $this->text()->null(),

        ], $tableOptions);

        $this->createTable('{{%student}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'department_id' => $this->integer()->null(),
            'education_direction_id' => $this->integer()->null(),
            'basis_of_learning' => $this->integer()->null(),
            'education_type' => $this->tinyInteger()->null(),

            'diploma_number' => $this->string()->null(),
            'diploma_date' => $this->timestamp()->null(),

            'type_of_residence' => $this->tinyInteger(1)->null(),
            'landlord_info' => $this->text()->null(),
            'student_live_with' => $this->text()->null(),
            'other_info' => $this->text()->null(),

        ], $tableOptions);


        // inserting data

        $this->insert('{{%users}}', [
            'username' => 'ShokirjonMK',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("12300123"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'mk@mk.com',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);



        $this->insert('{{%users}}', [
            'username' => 'suadmin',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("susu1221"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'suadmin@tsul.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);


        $this->insert('{{%users}}', [
            'username' => 'professor',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("prof007"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'admin@tsul.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);


        $this->insert('{{%users}}', [
            'username' => 'blackmoon',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("blackmoonuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'blackmoonuz@mail.ru',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
