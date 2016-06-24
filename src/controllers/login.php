<?php
use Symfony\Component\HttpFoundation\Request;

//login form
$app->get('/login', function() use ($app){
    if (is_logged_in($app['session'])) {
        return $app->redirect( APP_PATH );
    }
    return $app['twig']->render('login.twig');
});
//login action
$app->post('/login', function(Request $request) use ($app){

    $username = $request->get('username');
    $password = $request->get('password');

    $user_model = new User($app['db']);

    if($user_model->login($username, $password))
    {
        $app['session']->set('is_user', true);
        $app['session']->set('is_logged_in', true);
        $app['session']->set('user', $username);
        return $app->redirect( APP_PATH . '/index.php' );
    }
    else {
        return $app['twig']->render('login.twig', array('msg' => 'Incorrect login information'));
    }

    return $app['twig']->render('login.twig');
});

//logout
$app->get('/logout', function() use ($app){
    $app['session']->clear();
    $app['session']->getFlashBag()->add('msg', 'Successfully logged out.');
    return $app->redirect( APP_PATH . '/index.php/login' );
});