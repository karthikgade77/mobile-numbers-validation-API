<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

$_SERVER['HTTP_HOST'] = 'localhost:8080';
$_SERVER['DOCUMENT_ROOT'] = '/var/www/html/web';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
        ],
    ],
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'rules' => [
                'POST v1/auth' => 'v1/credential/auth',
                'GET v1/file/<id:\d+>' => 'v1/file/details',
                'POST v1/file' => 'v1/file/validate',
                'POST v1/phone' => 'v1/phone-number/validate',
            ]
        ],
        'user' => [
            'identityClass' => 'app\models\Credential',
            'enableAutoLogin' => false,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'wp1TCepyhg7qBXiQYA5Wcba2zo2FUAla',
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'class' => 'yii\web\Response'
        ],
    ],
    'params' => $params,
];
