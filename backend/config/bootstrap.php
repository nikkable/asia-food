<?php

use context\Commerce1C\Bootstrap as Commerce1CBootstrap;
use context\Commerce1C\config\Commerce1CConfig;

// Инициализируем Commerce1C Bootstrap
$config = new Commerce1CConfig(
    username: 'admin',
    password: 'password123',
    sessionTtlMinutes: 60,
    maxFileSize: 2097152, // 2MB
    version: '2.05'
);

$bootstrap = new Commerce1CBootstrap($config);
$bootstrap->bootstrap(Yii::$container);
