<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'app-vacations',
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'inertia'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'container' => [
        'definitions' => [
            // Режим аутентификации:
            // app\models\auth\LoginForm - с помощью формы ввода логина/пароля
            // app\models\auth\LoginLdap - без формы ввода логина/пароля, через LDAP-сервер
            'app\models\auth\LoginAbstract' => 'app\models\auth\LoginForm',
        ],
    ],
    'components' => [
        'request' => [
            'class' => 'tebe\inertia\web\Request',
            'cookieValidationKey' => '7d0d683457df8f6ff9d65e2b507c08cd'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/rules.php',
        ],
        'inertia' => [
            'class' => 'tebe\inertia\Inertia',
            'assetsDirs' => [
                '@webroot/assets/inertia'
            ],
        ],
        'session' => [
            'name' => '_Session',
            'savePath' => '@app/runtime/sessions'
        ],

        'roles' => 'app\components\Roles',

        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru-RU',
            'timeZone' => 'Asia/Yekaterinburg',
            'defaultTimeZone' => 'Asia/Yekaterinburg',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:d.m.Y H:i:s',
            'timeFormat' => 'php:H:i:s',
            'thousandSeparator' => ' ',
        ],

        // настройки подключения к ldap
        // только при установленном 'app\models\authenticate\IAuthenticate' => 'app\models\authenticate\Ldap'
        'ldap' =>
            // 'class' => 'app\components\ldap\Ldap',
            // 'connectionString' => 'ldap://n7701-dc10:389 ldap://n5001-dc07:389',
            // 'baseDn' => 'DC=regions,DC=tax,DC=nalog,DC=ru',
            array_merge(
                ['class' => 'app\components\ldap\Ldap'],
                require __DIR__ . '/ldap.php',
            ),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
