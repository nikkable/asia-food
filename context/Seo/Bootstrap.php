<?php

namespace context\Seo;

use context\Seo\config\SeoConfig;
use context\Seo\interfaces\SeoServiceInterface;
use context\Seo\services\SeoService;
use yii\base\BootstrapInterface;
use yii\di\Container;
use yii\web\Application;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if (!$app instanceof Application) {
            return;
        }

        $container = \Yii::$container;

        // Регистрируем конфигурацию
        $container->setSingleton(SeoConfig::class, function (Container $container) use ($app) {
            $siteUrl = $app->params['siteUrl'] ?? 'http://localhost:20080';
            return new SeoConfig(siteUrl: $siteUrl);
        });

        // Регистрируем сервис
        $container->setSingleton(SeoServiceInterface::class, function (Container $container) use ($app) {
            return new SeoService(
                $container->get(SeoConfig::class),
                $app->view
            );
        });
    }
}
