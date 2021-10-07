<?php return [
    'app_id' => 'NUIRG-52484aacc9343c1f4c8314efe1f2bf1e',
    'site_master_pass' => 310900,
    'domain_name' => 'localhost.loc',
    'api_url' => 'http://api.localhost.loc/',
    'assets_url' => 'http://assets.localhost.loc/',
    'admin_url' => 'http://admin.localhost.loc/',
    'site_url' => 'http://localhost.loc/',
    'local_cache' => false,
    'theme_force_copy' => false,
    'redis' => [
        'active' => false,
        'prefix' => 'mywebsite',
        'password' => '',
        'secret_key' => 'nuAwcdgs2bvKHhtQN3ZG06eDMCWRVrp4aF89jo5BPxJkST1fiY',
        'secret_iv' => '63ziB90kuKIJq5xcFVspXa4QMmGW1EYAglwH2LrnNDhOtSC7vP',
        'config' => [
            'host' => '127.0.0.1',
            'port' => '6379',
            'scheme' => 'tcp',
        ],
    ],
    'database' => [
        'db' => [
            'class' => 'yii\\db\\Connection',
            'dsn' => 'mysql:host=localhost;dbname=website_db',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'attributes' => [
                \yii\db\mssql\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode=(SELECT REPLACE(@@sql_mode,\'ONLY_FULL_GROUP_BY\',\'\'));',
            ],
        ],
    ],
    'mailer' => [
        'class' => 'yii\\swiftmailer\\Mailer',
        'useFileTransport' => true,
    ],
    'adminEmail' => 'admin@domain.com',
    'infoEmail' => 'info@domain.com',
    'supportEmail' => 'support@domain.com',
    'senderEmail' => 'noreply@domain.com',
    'senderName' => 'MY WEBSITE',
];