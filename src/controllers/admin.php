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

$app->mount('/admin', $admin);