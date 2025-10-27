<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'context\\File\\Bootstrap', 'context\\Seo\\Bootstrap', 'context\\Favorite\\Bootstrap', 'context\\Product\\Bootstrap', 'context\\Order\\Bootstrap', 'context\\Payment\\Bootstrap', 'repositories\\Bootstrap'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                // Главная страница
                '' => 'site/index',
                
                // Каталог и поиск
                'catalog' => 'catalog/index',
                'search' => 'search/index',
                
                // Категории (ЧПУ)
                'category/<slug:[\w\-]+>' => 'category/view',
                
                // Товары (ЧПУ) 
                'product/<slug:[\w\-]+>' => 'product/view',
                
                // Корзина
                'cart' => 'cart/index',
                'cart/add' => 'cart/add',
                'cart/update-quantity' => 'cart/update-quantity',
                'cart/remove' => 'cart/remove',
                'cart/quick-order' => 'cart/quick-order',
                
                // Избранное
                'favorites' => 'favorite/index',
                'favorites/add' => 'favorite/add',
                'favorites/remove' => 'favorite/remove',
                
                // Заказы
                'orders' => 'order/index',
                'orders/create' => 'order/create',
                'orders/view/<id:\d+>' => 'order/view',
                
                // Оплата
                'checkout' => 'payment/webhook', // Webhook для ЮKassa (алиас)
                'payment/pay/<orderId:\d+>' => 'payment/pay',
                'payment/success' => 'payment/success',
                'payment/fail' => 'payment/fail',
                'payment/webhook' => 'payment/webhook',
                
                // Статические страницы
                'about' => 'site/about',
                'contact' => 'site/contact',
                'delivery' => 'site/delivery',
                'payment' => 'site/payment',
                'privacy' => 'site/privacy',
                'terms' => 'site/terms',
                
                // Прокси для CommerceML
                'commerceml-proxy/<action:\w+>' => 'commerceml-proxy/<action>',
                
                // Изображения
                'image/resize' => 'image/resize',
                
                // Остальные правила
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
