<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$routes = include __DIR__ . '/routes.php';
$host_name = array_value($params, 'domain_name', get_host());

$main_config = array(
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'params' => $params,
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-store',
            'enableCsrfValidation' => true,
            'ipHeaders' => [
                'CF-Connecting-IP',
                'X-Forwarded-For',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => 'identity-user',
                'path' => '/',
            ],
        ],
        'session' => [
            'name' => 'store-sid',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $routes,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/translations',
                    'fileMap' => [
                        'app' => 'app.php',
                    ]
                ],
                'common*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/translations',
                    'fileMap' => [
                        'common' => 'app.php',
                    ]
                ],
            ],
        ],
    ],
    'on beforeRequest' => function () {
        $user = Yii::$app->user;
        $session = Yii::$app->session;
        $master_pass = false;
        $underconstruction_pass = false;
        $site_status = get_setting_value('site_status');
        $protected = get_setting_value('site_password_protection');

        if (!$user->isGuest) {
            $user_id = $user->getId();
            $roles = Yii::$app->authManager->getRolesByUser($user_id);

            if ($roles && isset($roles['admin'])) {
                $master_pass = true;
                $underconstruction_pass = true;
            }
        }

        // Check status
        if ($site_status != '1') {
            $underconstruction_check = $session->get('underconstruction_check');

            if ($underconstruction_check != 'pass' && !$underconstruction_pass) {
                Yii::$app->catchAll = ['site/maintenance'];
                return;
            }
        }

        // Check password protection
        if ($protected == '1') {
            $master_pass_check = $session->get('master_pass_check');

            if ($master_pass_check != 'pass' && !$master_pass) {
                Yii::$app->catchAll = ['site/store-locked'];
                return;
            }
        }

        // Check ajax request
        if (input_post('ajax') || input_post('xre')) {
            Yii::$app->catchAll = ['site/ajax'];
            return;
        }
    },
);

return check_app_config_files($main_config);
