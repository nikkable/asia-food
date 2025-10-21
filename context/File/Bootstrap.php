<?php

namespace context\File;

use context\File\interfaces\FileUploadServiceInterface;
use context\File\services\FileUploadService;
use context\File\interfaces\ImageResizeServiceInterface;
use context\File\services\ImageResizeService;
use Yii;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        Yii::$container->setDefinitions([
            FileUploadServiceInterface::class => FileUploadService::class,
            ImageResizeServiceInterface::class => ImageResizeService::class,
        ]);
    }
}
