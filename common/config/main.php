<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'container' => [
        'definitions' => [
            // Тут будут наши зависимости
        ],
        'singletons' => [
            // Тут будут синглтоны, если понадобятся
            'context\Product\interfaces\ProductServiceInterface' => 'context\Product\services\ProductService',
            'repositories\Product\interfaces\ProductRepositoryInterface' => 'repositories\Product\ProductRepository',
            'context\Category\interfaces\CategoryServiceInterface' => 'context\Category\services\CategoryService',
            'repositories\Category\interfaces\CategoryRepositoryInterface' => 'repositories\Category\CategoryRepository',

            // Cart
            'context\Cart\interfaces\CartServiceInterface' => 'context\Cart\services\CartService',
            'context\Cart\models\Cart' => 'context\Cart\models\Cart',

            // Order
            'context\Order\interfaces\OrderServiceInterface' => 'context\Order\services\OrderService',
            'repositories\Order\interfaces\OrderRepositoryInterface' => 'repositories\Order\OrderRepository',

            // File
            'context\File\interfaces\FileUploadServiceInterface' => 'context\File\services\FileUploadService',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            // uncomment if you want to cache RBAC items (`yii cache/flush` to clear cache)
            // 'cache' => 'cache',
        ],
    ],
];
