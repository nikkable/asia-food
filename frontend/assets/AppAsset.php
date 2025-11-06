<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/swiper-bundle.min.css',
        'css/style.min.css?clear=1122ssыs225',
    ];
    public $js = [
        'js/swiper-bundle.min.js',
        'js/notifications.js',
        'js/favorite.js',
        'js/cart.js',
        'js/price-formatter.js',
        'js/add-to-cart.js',
        'js/profile-autofill.js',
        'js/main.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}
