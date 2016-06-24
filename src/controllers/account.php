<?php

$account = $app['controllers_factory'];
$account->get('/', function() {
    return "Account Home";
});
$account->get('/edit', function() {
    return "Edit my account";
});

$app->mount('/account', $account);