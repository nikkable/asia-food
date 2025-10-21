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
use Yii;

class Bootstrap
{
    public function bootstrap(): void
    {
        Yii::$container->setDefinitions([
            Commerce1CConfig::class => Commerce1CConfig::class,
            CommerceSessionInterface::class => CommerceSessionService::class,
            CommerceAuthInterface::class => CommerceAuthService::class,
            CommerceImportInterface::class => CommerceImportService::class,
            CommerceProcessorInterface::class => CommerceProcessorService::class,
        ]);
    }
}
