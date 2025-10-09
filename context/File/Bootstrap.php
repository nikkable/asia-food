<?php

namespace context\File;

use context\File\interfaces\FileUploadServiceInterface;
use context\File\services\FileUploadService;
use yii\base\BootstrapInterface;
use yii\web\Application;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        \Yii::$container->set(FileUploadServiceInterface::class, FileUploadService::class);
    }
}