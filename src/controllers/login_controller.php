<?php
use Symfony\Component\HttpFoundation\Request;

// Home Page
$app->get('/', function() use ($app){
    if(!is_logged_in($app['session']))
    {
        return $app->redirect( APP_PATH . '/index.php/login' );
    }
    $workout_model = new Workout();
    $greeting = random_greeting();
    $user = $app['session']->get('user');
    $summary = $workout_model->user_home_data($user['id']);
    return $app['twig']->render('home.twig', array('user'=>$user, 'greeting' => $greeting, 'summary' => $summary));
});

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

    $user_model = new User();

    if($user_model->login($username, $password))
    {
        $app['session']->set('is_user', true);
        $app['session']->set('is_logged_in', true);
        return $app->redirect( APP_PATH . '/index.php' );
    }
    else {
        $app['session']->getFlashBag()->add('msg', 'Incorrect login information');
        $app['session']->getFlashBag()->add('msg_type', 'danger');
        return $app['twig']->render('login.twig');
    }

    return $app['twig']->render('login.twig');
});

//logout
$app->get('/logout', function() use ($app){
    $app['session']->clear();
    $app['session']->getFlashBag()->add('msg', 'Successfully logged out.');
    $app['session']->getFlashBag()->add('msg_type', 'success');
    return $app->redirect( APP_PATH . '/index.php/login' );
});

function random_greeting() {
    $greetings = array(
        'Hello',
        'Hey',
        'Yo',
        '\'Sup',
        'Hola'
    );
    shuffle($greetings);
    return $greetings[0];
}