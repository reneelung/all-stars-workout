<?php

$admin = $app['controllers_factory'];
$admin->get('/', function() {
    return 'Admin Home';
});

$admin->get('/users', function() use ($app) {
    $user_model = new User($app['db']);
    $users = $user_model->get_all();
    return print_r($users);
});

$admin->get('/scripts/add_users', function() use($app) {
    $users = array(

    );
});

$app->mount('/admin', $admin);