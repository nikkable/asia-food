<?php

namespace context\Commerce1C;

use context\Commerce1C\config\Commerce1CConfig;
use context\Commerce1C\interfaces\CommerceAuthInterface;
use context\Commerce1C\interfaces\CommerceSessionInterface;
use context\Commerce1C\interfaces\CommerceImportInterface;
use context\Commerce1C\interfaces\CommerceProcessorInterface;
use context\Commerce1C\services\CommerceAuthService;
use context\Commerce1C\services\CommerceSessionService;
use context\Commerce1C\services\CommerceImportService;
use context\Commerce1C\services\CommerceProcessorService;
use repositories\Commerce1C\interfaces\Commerce1CSyncRepositoryInterface;
use yii\di\Container;

class Bootstrap
{
    public function __construct(
        private Commerce1CConfig $config
    ) {}

    public function bootstrap(Container $container): void
    {
        // Регистрируем конфигурацию
        $container->setSingleton(Commerce1CConfig::class, $this->config);

        // Регистрируем сервисы
        $container->setSingleton(CommerceSessionInterface::class, CommerceSessionService::class);
        
        $container->setSingleton(CommerceAuthInterface::class, function() use ($container) {
            return new CommerceAuthService(
                $container->get(CommerceSessionInterface::class)
            );
        });

        $container->setSingleton(CommerceImportInterface::class, function() use ($container) {
            return new CommerceImportService(
                $container->get(CommerceSessionInterface::class),
                $container->get(Commerce1CSyncRepositoryInterface::class)
            );
        });

        $container->setSingleton(CommerceProcessorInterface::class, function() use ($container) {
            return new CommerceProcessorService(
                $container->get(CommerceAuthInterface::class),
                $container->get(CommerceImportInterface::class)
            );
        });
    }
}
