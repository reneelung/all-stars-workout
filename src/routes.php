<?php
use Symfony\Component\HttpFoundation\Request;

$workouts = $app['controllers_factory'];
$workouts->get('/', function() {
    return "Workouts Home";
});
$workouts->get('/add', function() {
    return "Add a workout";
});

$account = $app['controllers_factory'];
$account->get('/', function() {
    return "Account Home";
});
$account->get('/edit', function() {
    return "Edit my account";
});

$admin = $app['controllers_factory'];
$admin->get('/', function() {
    return 'Admin Home';
});

$admin->get('/users', function() {
    global $db;

    $query = "SELECT * FROM users";
    $users = $db->fetchAll($query);

    return print_r($users);
});

//login form
$app->get('/login', function() use ($app){
    return $app['twig']->render('login.twig');
});
//login action
$app->post('/login', function(Request $request) use ($app){
    $username = $request->get('username');
    $password = $request->get('password');
    if($username=='test' && $password=='test')
    {
        $app['session']->set('is_user', true);
        $app['session']->set('user', $username);
        return $app->redirect( APP_PATH . '/index.php' );
    }
    return $app['twig']->render('login.twig');
});

//secure page
$app->get('/', function() use ($app){
    if(!$app['session']->get('is_user'))
    {
        return $app->redirect( APP_PATH . '/index.php/login' );
    }
    return $app['twig']->render('home.twig', array('user'=>$app['session']->get('user')));
});

//logout
$app->get('/logout', function() use ($app){
    $app['session']->clear();
    return $app->redirect( APP_PATH . '/index.php/login' );
});

$app->get('/', function () {
    return 'Home Page';
});

$app->mount('/workouts', $workouts);
$app->mount('/account', $account);
$app->mount('/admin', $admin);