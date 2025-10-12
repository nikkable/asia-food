<?php

use context\Commerce1C\Bootstrap as Commerce1CBootstrap;
use context\Commerce1C\config\Commerce1CConfig;

// Регистрируем Commerce1C компонент
Yii::$app->set('commerce1c', function() {
    $config = new Commerce1CConfig(
        username: 'admin',
        password: 'password123',
        sessionTtlMinutes: 60,
        maxFileSize: 2097152, // 2MB
        version: '2.05'
    );
    
    $bootstrap = new Commerce1CBootstrap($config);
    $bootstrap->bootstrap(Yii::$container);
    
    return $bootstrap;
});
