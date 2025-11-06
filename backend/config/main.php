<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'name' => 'Asia Food',
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'container' => [
        'definitions' => [],
        'singletons' => [],
    ],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\db\*',
                    ],
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['info'],
                    'except' => [
                        'yii\db\*',
                        'application',
                    ],
                    'logFile' => '@runtime/logs/info.log',
                    'logVars' => [], // Отключаем автоматическое логирование переменных
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '1c' => 'commerce-ml/index',
                '1c/<action>' => 'commerce-ml/<action>',
            ],
        ],
    ],
    'params' => $params,
];
