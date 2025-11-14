<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => \yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
          ],
    ],
    'components' => [
        'log' => [
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

        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'dsn' => 'smtp://noreply@xn----7sbnkf1eg0g.xn--p1ai:rC8nW4aX6jrZ6gF0@mail.hosting.reg.ru:465?encryption=ssl',
            ],
            'messageConfig' => [
                'from' => ['noreply@xn----7sbnkf1eg0g.xn--p1ai' => 'Азия Фуд'],
            ],
        ],
    ],
    'params' => $params,
];
