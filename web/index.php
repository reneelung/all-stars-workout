<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

// Register Providers

use Silex\Provider\SessionServiceProvider;
$app->register(new SessionServiceProvider());

use Silex\Provider\TwigServiceProvider;
$app->register(new TwigServiceProvider());
$app['twig.loader.filesystem']->addPath(__DIR__ . '/../views/');

// Connect to DB
require_once __DIR__ . '/../src/db.php';

// Set Up Routes
require_once __DIR__ . '/../src/routes.php';

$app->run();