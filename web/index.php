<?php
    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../src/env.php';
    require_once __DIR__.'/../src/session.php';
    require_once __DIR__ . '/../src/models/user_model.php';
    require_once __DIR__ . '/../src/models/workout_model.php';

    $app = new Silex\Application();
    $app['debug'] = true;

    // Register Providers

    use Silex\Provider\SessionServiceProvider;
    $app->register(new SessionServiceProvider());

    use Silex\Provider\TwigServiceProvider;
    $app->register(new TwigServiceProvider());
    $app['twig.loader.filesystem']->addPath( VIEWS_PATH );
    $app->before(function() use ($app){
        $flash = $app['session']->getFlashBag();
        if($flash->has('msg'))
        {
            $msg = $flash->has('msg') ? $flash->get('msg') : '';
        }
        $app['twig']->addGlobal('msg', $msg[0]);
    });

    // Connect to DB
    require_once __DIR__ . '/../src/db.php';
    // Set Up Routes
    require_once __DIR__ . '/../src/routes.php';

    $app->run();