<?php
    $environment = $_SERVER['SERVER_NAME'] == 'localhost' ? 'dev' : 'prod';
    $path = $environment == 'dev' ? '/all-stars-workout/web' : '';

    define('ENVIRONMENT', $environment);
    define('APP_PATH', $path);
    define('VIEWS_PATH', __DIR__ . '/../views/');
