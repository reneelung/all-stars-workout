<?php
require_once __DIR__.'/../src/controllers/account.php';
require_once __DIR__.'/../src/controllers/admin.php';
require_once __DIR__.'/../src/controllers/login.php';
require_once __DIR__.'/../src/controllers/workouts.php';

// Home Page
$app->get('/', function() use ($app){
    if(!is_logged_in($app['session']))
    {
        return $app->redirect( APP_PATH . '/index.php/login' );
    }
    return $app['twig']->render('home.twig', array('user'=>$app['session']->get('user')));
});



