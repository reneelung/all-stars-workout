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
    $app->before(function() use ($app) {
        $flash = $app['session']->getFlashBag();
        $msg = $flash->has('msg') ? $flash->get('msg') : '';
        $msg_type = $flash->has('msg_type') ? $flash->get('msg_type') : '';
        $app['twig']->addGlobal('msg', $msg[0]);
        $app['twig']->addGlobal('msg_type', $msg_type[0]);
        $app['twig']->addGlobal('app_path', APP_PATH );
        $app['twig']->addGlobal('is_logged_in', $app['session']->get('is_logged_in'));
    });

    $app->register(new Silex\Provider\AssetServiceProvider(), array(
        'assets.version' => 'v1',
        'assets.version_format' => '%s?version=%s',
        'assets.named_packages' => array(
            'css' => array('version' => 'css2', 'base_path' => ASSETS_PATH )
        ),
    ));

    // Connect to DB
    require_once __DIR__ . '/../src/db.php';
    // Set Up Routes
    require_once __DIR__ . '/../src/routes.php';

    $app->run();