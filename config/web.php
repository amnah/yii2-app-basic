<?php

// ------------------------------------------------------------------------
// Main config
// ------------------------------------------------------------------------
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'UTC',
    'language' => 'en-US',
    'params' => require_once __DIR__ . '/params.php',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => env('YII_KEY'),
            'csrfCookie' => [ 'httpOnly' => true, 'secure' => isset($_SERVER['HTTPS']) ],
            'parsers' => [ 'application/json' => 'yii\web\JsonParser' ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'loginUrl' => ['auth/login'],
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity', 'httpOnly' => true, 'secure' => isset($_SERVER['HTTPS']) ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'cookieParams' => [ 'httpOnly' => true, 'secure' => isset($_SERVER['HTTPS']) ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'emailManager' => [
            'class' => 'app\components\EmailManager'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/views/emails',
            'useFileTransport' => env('MAIL_FILE_TRANSPORT'),
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => env('MAIL_HOST'),
                'port' => env('MAIL_PORT'),
                'username' => env('MAIL_USER'),
                'password' => env('MAIL_PASS'),
                'encryption' => env('MAIL_ENCRYPTION'),
            ],
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
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USER'),
            'password' => env('DB_PASS'),
            'tablePrefix' => env('DB_PREFIX'),
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
];

// ------------------------------------------------------------------------
// Debug and gii
// ------------------------------------------------------------------------
$debugConfig = [
    'class' => 'amnah\yii2\debug\Module',
    'allowedIPs' => ['*'],
    'panels' => [
        'user' => [
            'class'=>'yii\debug\panels\UserPanel',
            'ruleUserSwitch' => [ 'allow' => true ]
        ]
    ]
];
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = $debugConfig;

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
} elseif (isDebugEnabled()) {
    // enable debug for current ip
    $userIp = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = $debugConfig;
    $config['modules']['debug']['allowedIPs'] = [$userIp];
}


return $config;