<?php return [
    'app_id' => 'REOQA-7aef9430e4875b3d4dadff8dc39bf986',
    'site_master_pass' => 875337,
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
        'secret_key' => '4JGufNX5F1bL8IhetaTsM6HDxBogEncY9Vm0S7r2vjpQqzWyCK',
        'secret_iv' => 'cXZ1Yh2VDyoxvTERrN5jW3OwKIkbu8isqm9MaF4AQdnLP76Gfg',
        'config' => [
            'host' => '127.0.0.1',
            'port' => '6379',
            'scheme' => 'tcp',
        ],
    ],
    'database' => [
        'db' => [
            'class' => 'yii\\db\\Connection',
            'dsn' => 'mysql:host=localhost;dbname=kiu_api',
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
    'adminEmail' => 'mkshokirjon@gmail.com',
    'infoEmail' => 'info@domain.com',
    'supportEmail' => 'support@domain.com',
    'senderEmail' => 'noreply@domain.com',
    'senderName' => 'MY WEBSITE',
];
