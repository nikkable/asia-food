<?php

use yii\caching\FileCache;
use yii\db\Connection;

use repositories\Bootstrap as BootstrapRepositories;
use context\Favorite\Bootstrap as BootstrapFavorite;
use context\Cart\Bootstrap as BootstrapCart;
use context\Category\Bootstrap as BootstrapCategory;
use context\Product\Bootstrap as BootstrapProduct;
use context\Payment\Bootstrap as BootstrapPayment;
use context\Order\Bootstrap as BootstrapOrder;
use context\File\Bootstrap as BootstrapFile;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['log',
        BootstrapRepositories::class,
        BootstrapFavorite::class,
        BootstrapCart::class,
        BootstrapCategory::class,
        BootstrapProduct::class,
        BootstrapPayment::class,
        BootstrapOrder::class,
        BootstrapFile::class,
    ],
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=u3295317_default',
            'username' => 'u3295317_default',
            'password' => 'GlgaL3T4B2zrMi2B',
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru-RU',
            'defaultTimeZone' => 'Europe/Moscow',
            'currencyCode' => 'RUB',
            'numberFormatterSymbols' => [
                NumberFormatter::CURRENCY_SYMBOL => '₽',
            ],
            'numberFormatterOptions' => [
                NumberFormatter::MIN_FRACTION_DIGITS => 0,
                NumberFormatter::MAX_FRACTION_DIGITS => 0,
            ],
            'datetimeFormat' => 'php:d.m.Y H:i',
            'dateFormat' => 'php:d.m.Y',
            'timeFormat' => 'php:H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
        ],
    ],
];
